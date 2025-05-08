<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ✅ schedule the event status update every 5 minutes
app(Schedule::class)->command('app:updates')->everyMinute();
app(Schedule::class)->command('app:notify')->everyMinute();
