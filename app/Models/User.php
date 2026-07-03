<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'telegram_id',
        'first_name',
        'last_name',
        'username',
    ];

    public function measures()
    {
        return $this->hasMany(Measure::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}
