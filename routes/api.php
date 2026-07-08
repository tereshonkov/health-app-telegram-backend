<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeasureController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\PdfController;
use App\Http\Controllers\Api\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

// Публичный роут — авторизация
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:auth');
Route::post('/webhook/telegram', [TelegramWebhookController::class, 'handle']);

// Защищённые роуты — нужен токен
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::delete('measures/clear', [MeasureController::class, 'clearHistory']);
    Route::patch('reminders/{reminder}/toggle', [ReminderController::class, 'toggle']);
    Route::get('export/pdf', [PdfController::class, 'export']);

    Route::apiResource('measures', MeasureController::class);
    Route::apiResource('reminders', ReminderController::class);
});
