<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Keep-alive: ping self every minute to prevent Render from sleeping ──
Schedule::command('app:self-ping')->everyMinute()->withoutOverlapping();

