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
        public bool $enabled = true,
        public ?int $course_days = null,
    ) {}

    public static function fromArray(array $data, int $userId): self
    {
        return new self(
            user_id: $userId,
            name: $data['name'],
            dose: $data['dose'],
            times: array_map('strval', $data['times']),
            enabled: isset($data['enabled']) ? (bool) $data['enabled'] : true,
            course_days: isset($data['course_days']) && $data['course_days'] !== ''
                ? (int) $data['course_days']
                : null,
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
            'course_days' => $this->course_days,
            // started_at заповнюємо поточним часом якщо є курс
            'started_at'  => $this->course_days ? now() : null,
        ];
    }
}
