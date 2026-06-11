<?php

namespace Database\Factories;

use App\Enums\PayoutStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payout>
 */
class PayoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'instructor_id' => User::factory(),
            'idempotency_key' => Str::uuid(),
            'amount' => fake()->numberBetween(10, 500),
            'status' => fake()->randomElement(PayoutStatus::cases()),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => PayoutStatus::Pending]);
    }

    public function success(): static
    {
        return $this->state(fn () => ['status' => PayoutStatus::Success]);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => PayoutStatus::Failed]);
    }
}
