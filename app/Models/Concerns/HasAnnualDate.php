<?php

namespace App\Models\Concerns;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

trait HasAnnualDate
{
    public function scopeOnDate(Builder $query, CarbonInterface $date): Builder
    {
        return $query->where(function (Builder $q) use ($date) {
            $q->where('month', $date->month)->where('day', $date->day);

            if (! $date->isLeapYear() && $date->month === 2 && $date->day === 28) {
                $q->orWhere(fn(Builder $q) => $q->where('month', 2)->where('day', 29));
            }
        });
    }

    public function scopeNotCongratulatedIn(Builder $query, int $year): Builder
    {
        return $query->where(function (Builder $q) use ($year) {
            $q->whereNull('last_congratulated_year')
                ->orWhere('last_congratulated_year', '<', $year);
        });
    }
}
