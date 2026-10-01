<?php

use Illuminate\Support\Facades\Schedule;

// Data retention: remove candidate profiles past the retention period.
// Needs the scheduler running in production: `php artisan schedule:run` every minute.
Schedule::command('hdt:prune-candidates')->dailyAt('02:30');
