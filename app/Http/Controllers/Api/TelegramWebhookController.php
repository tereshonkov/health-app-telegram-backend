<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendReminderNotification;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramWebhookController extends Controller
{
    public function handle(): JsonResponse
    {
        $update = Telegram::getWebhookUpdate();

        // Обробка callback кнопок
        if ($update->callbackQuery) {
            $data = $update->callbackQuery->data;
            $chatId = $update->callbackQuery->message->chat->id;
            $messageId = $update->callbackQuery->message->messageId;

            if (str_starts_with($data, 'confirmed_')) {
                $reminderId = str_replace('confirmed_', '', $data);

                // Редагуємо повідомлення
                Http::post("https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/editMessageText", [
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => "✅ Принято!",
                ]);
            }

            if (str_starts_with($data, 'snooze_')) {
                $reminderId = str_replace('snooze_', '', $data);
                $reminder = Reminder::find($reminderId);

                if ($reminder) {
                    // Відправляємо через 30 хвилин
                    SendReminderNotification::dispatch($reminder)->delay(now()->addMinutes(30));

                    Http::post("https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/editMessageText", [
                        'chat_id' => $chatId,
                        'message_id' => $messageId,
                        'text' => "⏰ Напомню через 30 минут!",
                    ]);
                }
            }

            // Відповідаємо на callback щоб прибрати loading
            Http::post("https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/answerCallbackQuery", [
                'callback_query_id' => $update->callbackQuery->id,
            ]);

            return response()->json(['ok' => true]);
        }

        // Обробка команд
        Telegram::commandsHandler(true);
        return response()->json(['ok' => true]);
    }
}