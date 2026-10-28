<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Birthday extends Model
{
    protected $fillable = [
        'name',
        'day',
        'month',
        'year',
        'gender',
        'telegram_username',
        'last_congratulated_year',
    ];

    public function scopeOnDate(Builder $query, CarbonInterface $date): Builder
    {
        return $query
            ->where('month', $date->month)
            ->where('day', $date->day);
    }

    public function scopeNotCongratulatedIn(Builder $query, int $year): Builder
    {
        return $query->where(function (Builder $q) use ($year) {
            $q->whereNull('last_congratulated_year')
              ->orWhere('last_congratulated_year', '<', $year);
        });
    }
}