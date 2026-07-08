<?php

use App\Models\Reminder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use \App\Services\ReminderNotificationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $now = now()->format('H:i');

    \Illuminate\Support\Facades\Log::info('Scheduler running', ['time' => $now]);

    $reminders = Reminder::query()
        ->where('enabled', true)
        ->where('times', 'LIKE', "%\"{$now}\"%")
        ->with('user')
        ->get();

    \Illuminate\Support\Facades\Log::info('Reminders found', ['count' => $reminders->count()]);

    $reminders->each(function (\App\Models\Reminder $reminder) {
        app(ReminderNotificationService::class)->send($reminder);
    });
})->everyMinute();
