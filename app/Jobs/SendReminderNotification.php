<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Reminder;
use Illuminate\Support\Facades\Http;

class SendReminderNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Reminder $reminder
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->reminder->user;

        if (!$user->telegram_id) return;

        \Illuminate\Support\Facades\Log::info('Sending reminder', [
            'reminder' => $this->reminder->name,
            'telegram_id' => $user->telegram_id,
            'time' => now()->format('H:i'),
        ]);

        Http::post("https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/sendMessage", [
            'chat_id' => $user->telegram_id,
            'text' => "Привет! Пора принять *{$this->reminder->name}* ({$this->reminder->dose})",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [[
                    [
                        'text' => '✅ Принято!',
                        'callback_data' => "confirmed_{$this->reminder->id}",
                    ],
                    [
                        'text' => '⏰ Напомни через 30 минут',
                        'callback_data' => "snooze_{$this->reminder->id}",
                    ],
                ]],
            ]),
        ]);

        \Illuminate\Support\Facades\Log::info('Telegram response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);
    }
}
