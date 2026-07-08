<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    protected $fillable = [
        'user_id',
        'systolic',
        'diastolic',
        'pulse',
        'note',
    ];

    public function getStatus(): string
    {
        if ($this->systolic >= 180 || $this->diastolic >= 110 || $this->pulse >= 130) return 'critical';
        if ($this->systolic >= 140 || $this->diastolic >= 90 || $this->pulse >= 100) return 'high';
        if ($this->systolic >= 130 || $this->diastolic >= 85 || $this->pulse >= 90) return 'warn';
        return 'ok';
    }

    public function getStatusLabel(): string
    {
        return [
            'ok'       => 'Норма',
            'warn'     => 'Повышено',
            'high'     => 'Высокое',
            'critical' => 'Критично!',
        ][$this->getStatus()];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
