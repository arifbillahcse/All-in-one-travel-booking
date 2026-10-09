<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class BackupCommand extends Command
{
    protected $signature = 'travelorio:backup
        {--keep= : How many backups to keep (default: BACKUP_KEEP, 14)}
        {--no-files : Back up the database only}';

    protected $description = 'Back up the database and uploaded photos into one zip file';

    public function handle(BackupService $backups): int
    {
        try {
            $file = $backups->create(! $this->option('no-files'));
        } catch (\Throwable $e) {
            $this->error('Backup failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $size = round(filesize($file) / 1048576, 2);
        $this->info("Backup written: {$file} ({$size} MB)");

        // optional copy to another disk (S3, an SFTP server, ...): BACKUP_DISK in .env
        if ($disk = config('travelorio.backup.disk')) {
            Storage::disk($disk)->putFileAs('travelorio-backups', new File($file), basename($file));
            $this->info("Copied to the \"{$disk}\" disk.");
        }

        $deleted = $backups->prune((int) ($this->option('keep') ?: config('travelorio.backup.keep')));
        $deleted && $this->line('Removed '.count($deleted).' old backup(s).');

        return self::SUCCESS;
    }
}
