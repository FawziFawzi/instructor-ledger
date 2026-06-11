<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionInstructorShare>
 */
class SubscriptionInstructorShareFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'instructor_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 1, 40),
            'instructor_percentage' => fake()->numberBetween(1, 90),
        ];
    }
}
