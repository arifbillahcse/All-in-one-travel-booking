<?php

use Illuminate\Support\Facades\Schedule;

// Needs one cron line on the server:  * * * * * cd /var/www/travelorio && php artisan schedule:run >> /dev/null 2>&1
Schedule::command('travelorio:backup')->dailyAt('02:30')->onOneServer()->withoutOverlapping();
Schedule::command('queue:prune-failed --hours=168')->daily();
Schedule::command('queue:prune-batches --hours=48')->daily();
