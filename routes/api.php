<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeasureController;
use App\Http\Controllers\Api\ReminderController;
use Illuminate\Support\Facades\Route;

// Публичный роут — авторизация
Route::post('/auth/login', [AuthController::class, 'login']);

// Защищённые роуты — нужен токен
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('measures', MeasureController::class);
    Route::apiResource('reminders', ReminderController::class);
    Route::patch('reminders/{reminder}/toggle', [ReminderController::class, 'toggle']);
});
