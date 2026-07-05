<?php

use App\Jobs\SendReminderNotification;
use App\Models\Reminder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $now = now()->format('H:i');

    Reminder::query()
        ->where('enabled', true)
        ->whereJsonContains('times', $now)
        ->with('user')
        ->get()
        ->each(function (Reminder $reminder) {
            SendReminderNotification::dispatch($reminder);
        });
})->everyMinute();