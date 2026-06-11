<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 year', 'now');
        $durationDays = fake()->randomElement([30, 90, 365]);
        $end = (clone $start)->modify("+{$durationDays} days");

        return [
            'student_id' => User::factory(),
            'subscription_plan_id' => SubscriptionPlan::factory(),
            'amount' => fake()->randomElement([9.99, 19.99, 49.99]),
            'platform_fee_amount' => fake()->randomElement([1.00, 2.00, 5.00]),
            'status' => fake()->randomElement(SubscriptionStatus::cases()),
            'start_date' => $start,
            'end_date' => $end,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => SubscriptionStatus::Active]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => SubscriptionStatus::Cancelled]);
    }

    public function expired(): static
    {
        return $this->state(fn () => ['status' => SubscriptionStatus::Expired]);
    }
}
