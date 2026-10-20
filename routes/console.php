<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reminder:h1')->dailyAt('08:00');
Schedule::command('reminder:pengambilan')->dailyAt('09:00');
Schedule::command('reminder:hasil-belum-upload')->dailyAt('09:00');
Schedule::command('reminder:pelunasan')->dailyAt('08:00');
Schedule::command('reminder:h3jam')->everyThirtyMinutes();
Schedule::command('booking:batalkan-kedaluwarsa')->hourly();
//Schedule::command('booking:batalkan-kedaluwarsa')->everyMinute();
