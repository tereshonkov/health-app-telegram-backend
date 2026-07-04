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

        $botToken = config('services.telegram.bot_token');

        // Парсимо вручну зберігаючи оригінальні значення
        $pairs = explode('&', $initData);
        $params = [];
        foreach ($pairs as $pair) {
            $pos = strpos($pair, '=');
            if ($pos === false) continue;
            $key = substr($pair, 0, $pos);
            $value = substr($pair, $pos + 1);
            $params[$key] = urldecode($value);
        }

        $hash = $params['hash'] ?? null;
        if (!$hash) return null;

        // Видаляємо ТІЛЬКИ hash, signature залишаємо
        unset($params['hash']);

        // Сортуємо і збираємо рядок
        ksort($params);
        $dataCheckString = implode("\n", array_map(
            fn($k, $v) => "$k=$v",
            array_keys($params),
            array_values($params)
        ));

        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
        $expectedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (!hash_equals($expectedHash, $hash)) return null;

        $authDate = $params['auth_date'] ?? 0;
        if (time() - $authDate > 3600) return null;

        return json_decode($params['user'], true);
    }
}