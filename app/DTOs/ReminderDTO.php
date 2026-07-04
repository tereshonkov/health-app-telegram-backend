<?php

declare(strict_types=1);

namespace App\DTOs;

class ReminderDTO
{
    public function __construct(
        public int $user_id,
        public string $name,
        public string $dose,
        /** @var string[] $times */
        public array $times,
        public bool $enabled = true
    ) {}

    public static function fromArray(array $data, int $userId): self
    {
        return new self(
            user_id: $userId,
            name: $data['name'],
            dose: $data['dose'],
            times: array_map('strval', $data['times']),
            enabled: isset($data['enabled']) ? (bool) $data['enabled'] : true
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'name'    => $this->name,
            'dose'    => $this->dose,
            'times'   => $this->times,
            'enabled' => $this->enabled,
        ];
    }
}