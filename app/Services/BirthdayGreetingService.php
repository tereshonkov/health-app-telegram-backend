<?php

namespace App\Services;

use App\Models\Anniversary;
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

        $name = $this->formatName($birthday->name, $birthday->telegram_username);

        return str_replace('{name}', $name, Arr::random($templates));
    }

    public function buildAnniversaryMessage(Anniversary $anniversary): string
    {
        $templates = config('birthdays.anniversary_templates');

        if (empty($templates)) {
            throw new \RuntimeException('No anniversary templates configured');
        }

        $names = $this->formatName($anniversary->first_name, $anniversary->first_username)
            . ' и '
            . $this->formatName($anniversary->second_name, $anniversary->second_username);

        return str_replace('{names}', $names, Arr::random($templates));
    }

    private function formatName(string $name, ?string $username): string
    {
        return $username ? "{$name} (@{$username})" : $name;
    }
}
