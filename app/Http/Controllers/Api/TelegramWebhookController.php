<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramWebhookController extends Controller
{
    public function handle(): JsonResponse
    {
        Telegram::commandsHandler(true);
        return response()->json(['ok' => true]);
    }
}