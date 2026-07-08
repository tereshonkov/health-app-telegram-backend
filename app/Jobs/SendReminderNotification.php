<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Services\ReminderNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendReminderNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Reminder $reminder
    ) {}

    public function handle(ReminderNotificationService $service): void
    {
        $service->send($this->reminder);
    }
}
