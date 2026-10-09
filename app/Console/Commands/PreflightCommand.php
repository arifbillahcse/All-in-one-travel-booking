<?php

namespace App\Console\Commands;

use App\Models\Destination;
use App\Models\User;
use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Is this server ready for visitors?" Run it after every deployment.
 * FAIL = fix before going live, WARN = works but should be improved.
 */
class PreflightCommand extends Command
{
    protected $signature = 'travelorio:preflight {--strict : treat warnings as failures}';

    protected $description = 'Check that the server and settings are ready for production';

    /** @var list<array{0: string, 1: string, 2: string}> status, check, note */
    private array $results = [];

    public function handle(BackupService $backups): int
    {
        $this->environment();
        $this->database();
        $this->filesystem();
        $this->services($backups);
        $this->php();

        foreach ($this->results as [$status, $check, $detail]) {
            $label = match ($status) {
                'ok' => '<info> OK   </info>', 'warn' => '<comment> WARN </comment>', default => '<error> FAIL </error>',
            };
            $this->line("{$label} {$check}".($detail ? " <fg=gray>({$detail})</>" : ''));
        }

        $fails = count(array_filter($this->results, fn ($r) => $r[0] === 'fail'));
        $warns = count(array_filter($this->results, fn ($r) => $r[0] === 'warn'));
        $this->newLine();
        $this->line($fails ? "<error>{$fails} problem(s) to fix</error>" : '<info>Ready.</info>'.($warns ? " {$warns} warning(s)." : ''));

        return $fails || ($warns && $this->option('strict')) ? self::FAILURE : self::SUCCESS;
    }

    /** @param string $hint what to do, shown only when the check is not OK @param string $info a value, always shown */
    private function check(string $status, string $check, string $hint = '', string $info = ''): void
    {
        $this->results[] = [$status, $check, $status === 'ok' ? $info : trim($info.' '.$hint)];
    }

    private function environment(): void
    {
        $production = app()->environment('production');
        $this->check($production ? 'ok' : 'warn', 'APP_ENV is production', 'set APP_ENV=production', app()->environment());
        $this->check(config('app.debug') ? 'fail' : 'ok', 'APP_DEBUG is off', 'set APP_DEBUG=false: debug pages show passwords and code');
        $this->check(config('app.key') ? 'ok' : 'fail', 'APP_KEY is set');

        $url = (string) config('app.url');
        $this->check(str_starts_with($url, 'https://') && ! str_contains($url, 'localhost') ? 'ok' : 'fail', 'APP_URL is the real https address', 'set APP_URL=https://your-domain', $url);
        $this->check(config('travelorio.noindex') ? 'warn' : 'ok', 'Search engines may index the site', 'SITE_NOINDEX is on: right for staging, wrong for the live site');
        $this->check(config('app.timezone') === 'Asia/Dhaka' ? 'ok' : 'warn', 'Timezone is Asia/Dhaka', 'set APP_TIMEZONE=Asia/Dhaka', config('app.timezone'));
    }

    private function database(): void
    {
        try {
            DB::connection()->getPdo();
            $this->check('ok', 'Database connection', '', DB::connection()->getDriverName());
        } catch (\Throwable $e) {
            $this->check('fail', 'Database connection', $e->getMessage());

            return;
        }

        if (DB::connection()->getDriverName() === 'sqlite' && app()->environment('production')) {
            $this->check('warn', 'Database is MySQL/MariaDB', 'SQLite works for a small site, but MySQL is what the guide assumes');
        }

        $files = count(glob(database_path('migrations/*.php')));
        $ran = Schema::hasTable('migrations') ? DB::table('migrations')->count() : 0;
        $this->check($ran >= $files ? 'ok' : 'fail', 'All migrations have run', 'run php artisan migrate --force', "{$ran}/{$files}");

        if (Schema::hasTable('destinations')) {
            $this->check(Destination::published()->count() > 0 ? 'ok' : 'fail', 'Destinations exist', 'on a new site run php artisan db:seed --force');
        }

        if (Schema::hasTable('users')) {
            $this->check(User::where('role', User::ROLE_OWNER)->exists() ? 'ok' : 'fail', 'An owner account exists', 'run php artisan travelorio:admin you@example.com');
        }
    }

    private function filesystem(): void
    {
        foreach (['storage', 'storage/logs', 'storage/framework/cache', 'bootstrap/cache', 'storage/app/public'] as $dir) {
            $this->check(is_writable(base_path($dir)) ? 'ok' : 'fail', "{$dir} is writable by the web server");
        }

        $this->check(is_link(public_path('storage')) ? 'ok' : 'fail', 'public/storage link exists', 'run php artisan storage:link');
        $this->check(is_file(public_path('build/manifest.json')) ? 'ok' : 'fail', 'Assets are built', 'run npm run build');
        $this->check(is_file(base_path('bootstrap/cache/config.php')) ? 'ok' : 'warn', 'Configuration is cached', 'run php artisan optimize');
        $this->check(! is_file(public_path('hot')) ? 'ok' : 'fail', 'No Vite dev server marker (public/hot)');
    }

    private function services(BackupService $backups): void
    {
        $this->check(in_array(config('cache.default'), ['array', 'null'], true) ? 'fail' : 'ok', 'Cache store keeps data', 'use CACHE_STORE=file or redis', config('cache.default'));
        $this->check(config('session.driver') === 'array' ? 'fail' : 'ok', 'Sessions are kept', 'use SESSION_DRIVER=file or database', config('session.driver'));
        $this->check(config('queue.default') === 'sync' ? 'warn' : 'ok', 'Queue worker is used', 'QUEUE_CONNECTION=sync works, but photo resizing then happens during the upload request');

        $mailer = config('mail.default');
        $this->check(in_array($mailer, ['log', 'array'], true) ? 'fail' : 'ok', 'Email is really sent', 'set MAIL_MAILER=smtp: inquiries would only be written to the log', "MAIL_MAILER={$mailer}");
        $this->check(filled(site('notify_email') ?: site('email')) ? 'ok' : 'fail', 'Inquiry notification address is set', 'set it in Admin → Site settings');
        $this->check(config('logging.default') === 'stack' && config('logging.channels.stack.channels') === ['single'] ? 'warn' : 'ok', 'Logs rotate daily', 'set LOG_STACK=daily');

        $last = $backups->list()[0] ?? null;
        $age = $last ? (time() - filemtime($last)) / 3600 : null;
        $this->check($last && $age < 48 ? 'ok' : 'warn', 'A backup from the last 2 days exists', 'run php artisan travelorio:backup and check the cron line', $last ? round($age).' hours ago' : 'none yet');
    }

    private function php(): void
    {
        $this->check(PHP_VERSION_ID >= 80300 ? 'ok' : 'warn', 'PHP 8.3 or newer', 'upgrade PHP', PHP_VERSION);

        foreach (['intl', 'gd', 'zip', 'mbstring', 'fileinfo', 'exif', 'pdo_mysql'] as $extension) {
            $this->check(extension_loaded($extension) ? 'ok' : ($extension === 'pdo_mysql' ? 'warn' : 'fail'), "PHP extension {$extension}");
        }

        $this->check(extension_loaded('gd') && ! empty(gd_info()['WebP Support']) ? 'ok' : 'fail', 'GD can write WebP (photo resizing)');
        $this->check(function_exists('opcache_get_status') && ini_get('opcache.enable') ? 'ok' : 'warn', 'OPcache is on (much faster PHP)');

        foreach (['upload_max_filesize' => 12, 'post_max_size' => 12, 'memory_limit' => 256] as $setting => $minimumMb) {
            $mb = $this->megabytes(ini_get($setting));
            $this->check($mb >= $minimumMb ? 'ok' : 'fail', "{$setting} is at least {$minimumMb}M", "raise {$setting} in php.ini", (string) ini_get($setting));
        }
    }

    /** php.ini sizes ("2M", "1G", "-1") in megabytes; -1 (no limit) counts as unlimited. */
    private function megabytes(string|false $value): float
    {
        $number = (float) $value;

        if ($number < 0) {
            return INF;
        }

        return match (strtolower(substr((string) $value, -1))) {
            'g' => $number * 1024,
            'k' => $number / 1024,
            'm' => $number,
            default => $number / 1048576,
        };
    }
}
