<?php

namespace App\Models;

use App\Models\Concerns\HasAnnualDate;
use Illuminate\Database\Eloquent\Model;

class Birthday extends Model
{
    use HasAnnualDate;

    protected $fillable = [
        'name',
        'day',
        'month',
        'year',
        'gender',
        'telegram_username',
        'last_congratulated_year',
    ];
}
