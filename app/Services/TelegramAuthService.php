<?php

namespace App\Services;

class TelegramAuthService
{
    public function validate(string $initData): array|null
    {
        if (app()->environment('local') && $initData === 'dev') {
            return [
                'id'         => 1,
                'first_name' => 'Марія',
                'last_name'  => null,
                'username'   => 'maria_test',
            ];
        }

        \Illuminate\Support\Facades\Log::info('initData received', ['data' => $initData]);

        $botToken = config('services.telegram.bot_token');

        // Парсим initData
        parse_str($initData, $params);

        $hash = $params['hash'] ?? null;
        if (!$hash) return null;

        // Убираем hash из параметров
        unset($params['hash']);

        // Сортируем и собираем строку для проверки
        ksort($params);
        $dataCheckString = implode("\n", array_map(
            fn($k, $v) => "$k=$v",
            array_keys($params),
            array_values($params)
        ));

        // Генерируем секретный ключ
        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);

        // Проверяем подпись
        $expectedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (!hash_equals($expectedHash, $hash)) return null;

        // Проверяем что данные не устарели (1 час)
        $authDate = $params['auth_date'] ?? 0;
        if (time() - $authDate > 3600) return null;

        // Возвращаем данные юзера
        return json_decode($params['user'], true);
    }
}
