<?php

namespace App\Services;

use App\Models\Birthday;
use Illuminate\Support\Arr;

class BirthdayGreetingService
{
    public function buildMessage(Birthday $birthday): string
    {
        $templates = config("birthdays.templates.{$birthday->gender}");

        if (empty($templates)) {
            throw new \RuntimeException("No birthday templates for gender: {$birthday->gender}");
        }

        $name = $birthday->telegram_username
            ? "{$birthday->name} (@{$birthday->telegram_username})"
            : $birthday->name;

        return str_replace('{name}', $name, Arr::random($templates));
    }
}
