<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeasureController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\PdfController;
use Illuminate\Support\Facades\Route;

// Публичный роут — авторизация
Route::post('/auth/login', [AuthController::class, 'login']);

// Защищённые роуты — нужен токен
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('measures/clear', [MeasureController::class, 'clearHistory']);
    Route::patch('reminders/{reminder}/toggle', [ReminderController::class, 'toggle']);
    Route::get('export/pdf', [PdfController::class, 'export']);
    Route::post('/webhook/telegram', [TelegramWebhookController::class, 'handle']);

    Route::apiResource('measures', MeasureController::class);
    Route::apiResource('reminders', ReminderController::class);
});
