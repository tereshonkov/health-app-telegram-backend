# Health App API

Laravel REST API for Telegram Mini App — blood pressure & medication tracker.

## Auth

Telegram `initData` → HMAC-SHA256 validation → Sanctum token

## Setup

```bash
composer install && cp .env.example .env
php artisan key:generate && php artisan migrate
php artisan serve
```

## Env

```env
TELEGRAM_BOT_TOKEN=your_token
DB_DATABASE=health_app
```
