<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class RestoreCommand extends Command
{
    protected $signature = 'travelorio:restore
        {file? : Backup zip (default: the newest one)}
        {--force : Do not ask for confirmation}
        {--no-safety : Do not take a safety backup of the current data first}';

    protected $description = 'Replace the database and uploaded photos with the contents of a backup';

    public function handle(BackupService $backups): int
    {
        $file = $this->argument('file') ?: ($backups->list()[0] ?? null);

        if (! $file || ! is_file($file)) {
            $this->error('No backup file found. Pass the path of a travelorio-*.zip file.');

            return self::FAILURE;
        }

        $this->warn("This replaces ALL current content (database and photos) with {$file}.");

        if (! $this->option('force') && ! $this->confirm('Continue?')) {
            return self::FAILURE;
        }

        try {
            if (! $this->option('no-safety')) {
                $this->line('Safety backup of the current data: '.$backups->create());
            }

            $backups->restore($file);
        } catch (\Throwable $e) {
            $this->error('Restore failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->call('cache:clear');
        $this->info('Restored. Check the site and the admin panel.');

        return self::SUCCESS;
    }
}
