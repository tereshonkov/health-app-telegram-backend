<?php

namespace Database\Seeders;

use App\Models\Birthday;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BirthdaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Birthday::insert([
            ['name' => 'Тест Мужчина', 'day' => 28, 'month' => 10, 'year' => 1995, 'gender' => 'male',   'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Тест Женщина', 'day' => 28, 'month' => 10, 'year' => null, 'gender' => 'female', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Не сегодня',   'day' => 15, 'month' => 3,  'year' => 1990, 'gender' => 'male',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
