<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;
use ZipArchive;

/**
 * Backups of everything that cannot be rebuilt from the code: the database and the uploaded photos.
 *
 * A backup is one zip file: database.sql (MySQL) or database.sqlite, the photos under uploads/,
 * and manifest.json with the time, the database type and a checksum of the dump.
 */
class BackupService
{
    public function directory(): string
    {
        $path = config('travelorio.backup.path');
        is_dir($path) || mkdir($path, 0750, true);

        return $path;
    }

    /** @return string path of the new backup */
    public function create(bool $withFiles = true): string
    {
        $driver = DB::connection()->getDriverName();
        $work = sys_get_temp_dir().'/travelorio-backup-'.bin2hex(random_bytes(6));
        mkdir($work, 0700, true);

        try {
            $dump = match ($driver) {
                'mysql', 'mariadb' => $this->dumpMysql($work),
                'sqlite' => $this->dumpSqlite($work),
                default => throw new RuntimeException("Backups are not supported for the {$driver} database."),
            };

            $target = $this->uniqueTarget();
            $zip = new ZipArchive;

            if ($zip->open($target, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('Could not create the backup file.');
            }

            $zip->addFile($dump, basename($dump));
            $files = $withFiles ? $this->addUploads($zip) : 0;
            $zip->addFromString('manifest.json', json_encode([
                'app' => config('app.name'),
                'created_at' => now()->toIso8601String(),
                'database' => $driver === 'sqlite' ? 'sqlite' : 'mysql',
                'database_file' => basename($dump),
                'database_sha256' => hash_file('sha256', $dump),
                'uploads' => $files,
            ], JSON_PRETTY_PRINT));
            $zip->close();
            chmod($target, 0600);

            return $target;
        } finally {
            $this->removeDirectory($work);
        }
    }

    /** Keep the newest $keep backups, delete the others. @return list<string> deleted files */
    public function prune(int $keep): array
    {
        $files = $this->list();
        $deleted = [];

        foreach (array_slice($files, $keep) as $file) {
            unlink($file);
            $deleted[] = $file;
        }

        return $deleted;
    }

    /** @return list<string> backups, newest first */
    public function list(): array
    {
        $files = glob($this->directory().'/travelorio-*.zip') ?: [];
        // newest first; the name's timestamp decides, a same-second suffix (-2) breaks ties
        usort($files, fn ($a, $b) => strnatcmp(basename($b, '.zip').'', basename($a, '.zip').'') ?: 0);

        return $files;
    }

    public function restore(string $file): void
    {
        $zip = new ZipArchive;

        if ($zip->open($file) !== true) {
            throw new RuntimeException('This is not a readable backup file.');
        }

        $manifest = json_decode((string) $zip->getFromName('manifest.json'), true);
        $databaseFile = $manifest['database_file'] ?? null;

        if (! $manifest || ! in_array($databaseFile, ['database.sql', 'database.sqlite'], true)) {
            throw new RuntimeException('The backup has no valid manifest.');
        }

        $work = sys_get_temp_dir().'/travelorio-restore-'.bin2hex(random_bytes(6));
        mkdir($work, 0700, true);

        try {
            $zip->extractTo($work, $databaseFile);
            $dump = $work.'/'.$databaseFile;

            if (! is_file($dump) || ! hash_equals($manifest['database_sha256'], hash_file('sha256', $dump))) {
                throw new RuntimeException('The database in this backup is damaged (checksum mismatch). Nothing was changed.');
            }

            $driver = DB::connection()->getDriverName();
            $expected = $driver === 'sqlite' ? 'sqlite' : 'mysql';

            if ($manifest['database'] !== $expected) {
                throw new RuntimeException("This backup is from a {$manifest['database']} database but the site uses {$expected}.");
            }

            $driver === 'sqlite' ? $this->restoreSqlite($dump) : $this->restoreMysql($dump);
            $this->restoreUploads($zip);
        } finally {
            $zip->close();
            $this->removeDirectory($work);
        }
    }

    // ---- databases ----------------------------------------------------------------------------------

    private function dumpSqlite(string $work): string
    {
        $target = $work.'/database.sqlite';
        // VACUUM INTO writes a consistent copy even while the site is being used
        DB::connection()->statement('VACUUM INTO '.DB::connection()->getPdo()->quote($target));

        return $target;
    }

    /** Two backups in the same second must never share a file name (a safety backup would overwrite the one being restored). */
    private function uniqueTarget(): string
    {
        $base = $this->directory().'/travelorio-'.now()->format('Ymd-His');
        $target = $base.'.zip';

        for ($i = 2; file_exists($target); $i++) {
            $target = $base.'-'.$i.'.zip';
        }

        return $target;
    }

    private function restoreSqlite(string $dump): void
    {
        $connection = DB::connection();
        $path = $connection->getDatabaseName();

        if ($path === ':memory:' || $path === '') {
            throw new RuntimeException('Cannot restore into an in-memory database.');
        }

        DB::disconnect();
        copy($dump, $path);
        DB::purge();
    }

    private function dumpMysql(string $work): string
    {
        $target = $work.'/database.sql';
        $process = new Process(array_merge(['mysqldump'], $this->mysqlArguments(), [
            '--single-transaction', '--quick', '--routines', '--no-tablespaces', '--default-character-set=utf8mb4',
            '--result-file='.$target, config('database.connections.'.config('database.default').'.database'),
        ]), null, $this->mysqlEnvironment(), null, 600);
        $process->run();

        if (! $process->isSuccessful() || ! is_file($target)) {
            throw new RuntimeException('mysqldump failed: '.trim($process->getErrorOutput()));
        }

        return $target;
    }

    private function restoreMysql(string $dump): void
    {
        $process = new Process(array_merge(['mysql'], $this->mysqlArguments(), [
            '--default-character-set=utf8mb4', config('database.connections.'.config('database.default').'.database'),
        ]), null, $this->mysqlEnvironment(), fopen($dump, 'r'), 600);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException('mysql import failed: '.trim($process->getErrorOutput()));
        }
    }

    /** @return list<string> connection options (the password goes through the environment, not the command line) */
    public function mysqlArguments(): array
    {
        $c = config('database.connections.'.config('database.default'));

        return array_values(array_filter([
            '--host='.($c['host'] ?? '127.0.0.1'),
            '--port='.($c['port'] ?? 3306),
            '--user='.($c['username'] ?? 'root'),
            ! empty($c['unix_socket']) ? '--socket='.$c['unix_socket'] : null,
        ]));
    }

    /** @return array<string, string> */
    private function mysqlEnvironment(): array
    {
        return ['MYSQL_PWD' => (string) (config('database.connections.'.config('database.default').'.password') ?? '')];
    }

    // ---- uploads --------------------------------------------------------------------------------------

    private function uploadsRoot(): string
    {
        return rtrim(Storage::disk('public')->path(''), '/');
    }

    private function addUploads(ZipArchive $zip): int
    {
        $root = $this->uploadsRoot();
        $count = 0;

        if (! is_dir($root)) {
            return 0;
        }

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            if ($file->isFile() && ! $file->isLink()) {
                $zip->addFile($file->getPathname(), 'uploads/'.ltrim(substr($file->getPathname(), strlen($root)), '/'));
                $count++;
            }
        }

        return $count;
    }

    private function restoreUploads(ZipArchive $zip): void
    {
        $root = $this->uploadsRoot();

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (! str_starts_with($name, 'uploads/') || str_ends_with($name, '/')) {
                continue;
            }

            $relative = substr($name, strlen('uploads/'));

            // never write outside the uploads folder
            if (str_contains($relative, '..') || str_starts_with($relative, '/') || str_contains($relative, "\0")) {
                throw new RuntimeException("The backup contains an unsafe file name: {$name}");
            }

            $target = $root.'/'.$relative;
            is_dir(dirname($target)) || mkdir(dirname($target), 0755, true);
            file_put_contents($target, $zip->getStream($name));
        }
    }

    private function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($dir);
    }
}
