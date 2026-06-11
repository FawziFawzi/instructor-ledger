<?php

namespace Database\Seeders;

use App\Models\InstructorBalance;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstructorBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('type', 'instructor')->get();

        foreach ($instructors as $instructor) {
            InstructorBalance::factory()->create([
                'instructor_id' => $instructor->id,
            ]);
        }
    }
}
