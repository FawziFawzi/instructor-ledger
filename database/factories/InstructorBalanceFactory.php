<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InstructorBalance>
 */
class InstructorBalanceFactory extends Factory
{
    public function definition(): array
    {
        $available = fake()->randomFloat(2, 0, 500);
        $pending = fake()->randomFloat(2, 0, 200);

        return [
            'instructor_id' => User::factory(),
            'available_balance' => $available,
            'pending_balance' => $pending,
            'total_balance' => round($available + $pending, 2),
        ];
    }
}
