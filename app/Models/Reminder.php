<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'dose',
        'times',
        'enabled',
        'course_days',
        'started_at'
    ];

    protected $casts = [
        'times'   => 'array',
        'enabled' => 'boolean',
        'started_at' => 'datetime',
    ];

    // Метод для підрахунку днів що залишились
    public function daysLeft(): ?int
    {
        if (!$this->course_days || !$this->started_at) return null;

        $endDate = $this->started_at->copy()->addDays($this->course_days);
        $left = now()->startOfDay()->diffInDays($endDate->startOfDay(), false);

        return max(0, (int) $left);
    }

    public function isCourseFinished(): bool
    {
        if (!$this->course_days || !$this->started_at) return false;

        $endDate = $this->started_at->copy()->addDays($this->course_days);
        return now()->startOfDay()->greaterThanOrEqualTo($endDate->startOfDay());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
