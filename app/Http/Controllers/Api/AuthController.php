<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TelegramAuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private TelegramAuthService $telegramAuth
    ) {}

    public function login(Request $request)
    {
        $initData = $request->input('initData');

        if (!$initData) {
            return response()->json(['error' => 'initData is required'], 422);
        }

        // Валидируем подпись Telegram
        $userData = $this->telegramAuth->validate($initData);

        if (!$userData) {
            return response()->json(['error' => 'Invalid initData'], 401);
        }

        // Находим или создаём пользователя
        $user = User::updateOrCreate(
            ['telegram_id' => $userData['id']],
            [
                'first_name' => $userData['first_name'] ?? '',
                'last_name'  => $userData['last_name'] ?? null,
                'username'   => $userData['username'] ?? null,
            ]
        );

        // Удаляем старые токены и создаём новый
        // $user->tokens()->delete();

        // Створюємо новий токен, лишаючи старі для інших пристроїв
        // але видаляємо токени старші 30 днів щоб не накопичувались
        $user->tokens()
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        $token = $user->createToken('telegram')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'username'   => $user->username,
            ],
        ]);
    }
}
