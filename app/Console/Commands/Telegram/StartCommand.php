<?php

namespace App\Console\Commands\Telegram;

use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Запуск приложения';

    public function handle(): void
    {
        $this->replyWithMessage([
            'text' => '👋 Привет! Нажми кнопку ниже что бы открыть приложение.',
            'reply_markup' => json_encode([
                'inline_keyboard' => [[
                    [
                        'text' => '❤️ Открыть Health Tracker',
                        'web_app' => [
                            'url' => env('FRONTEND_URL'),
                        ],
                    ],
                ]],
            ]),
        ]);
    }
}