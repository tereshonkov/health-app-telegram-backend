<?php

use App\Models\Reminder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use \App\Services\ReminderNotificationService;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $now = now()->format('H:i');

    Log::info('Scheduler running', ['time' => $now]);

    // Спочатку перевіряємо завершені курси
    $finishedCourses = Reminder::query()
        ->where('enabled', true)
        ->whereNotNull('course_days')
        ->whereNotNull('started_at')
        ->with('user')
        ->get()
        ->filter(fn($r) => $r->isCourseFinished());

    foreach ($finishedCourses as $reminder) {
        app(ReminderNotificationService::class)->sendCourseFinished($reminder);
        $reminder->update(['enabled' => false]);
    }

    $reminders = Reminder::query()
        ->where('enabled', true)
        ->where('times', 'LIKE', "%\"{$now}\"%")
        ->with('user')
        ->get();

    Log::info('Reminders found', ['count' => $reminders->count()]);

    $reminders->each(function (\App\Models\Reminder $reminder) {
        app(ReminderNotificationService::class)->send($reminder);
    });
})->everyMinute();
