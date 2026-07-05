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
    
    \Illuminate\Support\Facades\Log::info('Scheduler running', ['time' => $now]);
    
    $reminders = Reminder::query()
        ->where('enabled', true)
        ->whereJsonContains('times', $now)
        ->with('user')
        ->get();
    
    \Illuminate\Support\Facades\Log::info('Reminders found', ['count' => $reminders->count()]);
    
    $reminders->each(function (Reminder $reminder) {
        SendReminderNotification::dispatch($reminder);
    });
})->everyMinute();