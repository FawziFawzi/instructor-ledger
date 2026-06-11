<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    public function definition(): array
    {
        $plans = [
            ['name' => 'Monthly Plan', 'slug' => 'monthly', 'price' => 999,  'duration_days' => 30],
            ['name' => '3-Month Plan', 'slug' => '3-month', 'price' => 1999, 'duration_days' => 90],
            ['name' => 'Annual Plan',  'slug' => 'annual',  'price' => 4999, 'duration_days' => 365],
        ];

        $plan = fake()->randomElement($plans);

        return [
            'name'          => $plan['name'],
            'slug'          => $plan['slug'],
            'price'         => $plan['price'],
            'duration_days' => $plan['duration_days'],
            'is_active'     => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
