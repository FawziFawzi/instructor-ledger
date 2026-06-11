<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 5 instructors
        User::factory(5)->create(['type' => 'instructor']);

        // 10 students
        User::factory(10)->create(['type' => 'student']);
    }
}
