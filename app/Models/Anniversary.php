<?php

namespace App\Models;

use App\Models\Concerns\HasAnnualDate;
use Illuminate\Database\Eloquent\Model;

class Anniversary extends Model
{
    use HasAnnualDate;

    protected $fillable = [
        'first_name',
        'first_username',
        'second_name',
        'second_username',
        'day',
        'month',
        'year',
        'last_congratulated_year',
    ];
}