<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    DB::table('selections')
            ->where('registration_deadline', '<', now()->toDateString())
            ->where('is_ended', 0)
            ->update(['is_ended' => 1]);
})->daily();