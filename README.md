# Health App — Backend API

Laravel 13 REST API for Telegram Mini App [@margoheal_bot](https://t.me/margoheal_bot/health) — blood pressure, pulse tracker and medication reminders.

## Stack

- **Laravel 13** — PHP framework
- **Laravel Sanctum** — token-based authentication
- **MySQL** — database
- **DomPDF** — PDF generation
- **Telegram Bot SDK** — bot integration
- **GCP e2-micro** — hosting (Always Free tier)

## Auth

Telegram Mini App `initData` → HMAC-SHA256 validation → Sanctum token

## Endpoints

### Public
| Method | URL | Description |
|--------|-----|-------------|
| POST | `/api/auth/login` | Validate initData, return token |
| POST | `/api/webhook/telegram` | Telegram bot webhook |

### Protected (Bearer token)
| Method | URL | Description |
|--------|-----|-------------|
| GET | `/api/measures` | Get measures |
| POST | `/api/measures` | Add measure |
| DELETE | `/api/measures/{id}` | Delete measure |
| DELETE | `/api/measures/clear` | Clear all measures |
| GET | `/api/reminders` | Get reminders |
| POST | `/api/reminders` | Add reminder |
| PATCH | `/api/reminders/{id}/toggle` | Toggle reminder |
| DELETE | `/api/reminders/{id}` | Delete reminder |
| GET | `/api/export/pdf` | Send PDF report via bot |

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Environment

```env
APP_ENV=production
APP_URL=https://your-domain.com
DB_DATABASE=health_app
DB_USERNAME=root
DB_PASSWORD=
TELEGRAM_BOT_TOKEN=your_bot_token
FRONTEND_URL=https://your-frontend.com
```

## Deploy

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan migrate --force
```