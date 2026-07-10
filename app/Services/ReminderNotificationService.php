<?php

namespace App\Services;

use App\Models\Reminder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReminderNotificationService
{
    public function send(Reminder $reminder): void
    {
        $user = $reminder->user;

        if (!$user->telegram_id) return;

        Log::info('Sending reminder', [
            'reminder'    => $reminder->name,
            'telegram_id' => $user->telegram_id,
            'time'        => now()->format('H:i'),
        ]);

        $response = Http::post("https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/sendMessage", [
            'chat_id'      => $user->telegram_id,
            'text'         => "Привет! Пора принять *{$reminder->name}* ({$reminder->dose})",
            'parse_mode'   => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [[
                    ['text' => '✅ Принято!', 'callback_data' => "confirmed_{$reminder->id}"],
                    ['text' => '⏰ Напомни через 30 минут', 'callback_data' => "snooze_{$reminder->id}"],
                ]],
            ]),
        ]);

        Log::info('Telegram response', [
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);
    }

    public function sendCourseFinished(Reminder $reminder): void
    {
        $user = $reminder->user;

        if (!$user->telegram_id) return;

        Http::post("https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/sendMessage", [
            'chat_id'    => $user->telegram_id,
            'text'       => "✅ Курс приёма *{$reminder->name}* ({$reminder->dose}) завершён!\n\nНапоминания отключены. Будьте здоровы! 💙",
            'parse_mode' => 'Markdown',
        ]);
    }
}
