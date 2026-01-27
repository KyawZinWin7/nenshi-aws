<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('db:backup')->dailyAt('09:00');



//  Auto complete sizing operations


Schedule::command('sizing:auto-complete')
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer();
