<?php

namespace App\DTOs;

class MeasureDTO
{
    public function __construct(
        public int $user_id,
        public ?int $systolic = null,
        public ?int $diastolic = null,
        public ?int $pulse = null,
        public ?string $note = null
    ) {}

    public static function fromArray(array $data, int $userId): self
    {
        return new self(
            user_id: $userId,
            systolic: isset($data['systolic']) && $data['systolic'] !== '' ? (int) $data['systolic'] : null,
            diastolic: isset($data['diastolic']) && $data['diastolic'] !== '' ? (int) $data['diastolic'] : null,
            pulse: isset($data['pulse']) && $data['pulse'] !== '' ? (int) $data['pulse'] : null,
            note: $data['note'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'systolic' => $this->systolic,
            'diastolic' => $this->diastolic,
            'pulse' => $this->pulse,
            'note' => $this->note,
        ];
    }
}
