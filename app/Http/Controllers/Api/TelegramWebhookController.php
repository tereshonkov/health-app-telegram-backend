<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramWebhookController extends Controller
{
    public function handle(): void
    {
        Telegram::commandsHandler(true);
    }
}