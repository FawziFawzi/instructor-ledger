<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['name' => 'Monthly Plan', 'slug' => 'monthly', 'price' => 999,  'duration_days' => 30,  'is_active' => true],
            ['name' => '3-Month Plan', 'slug' => '3-month', 'price' => 1999, 'duration_days' => 90,  'is_active' => true],
            ['name' => 'Annual Plan',  'slug' => 'annual',  'price' => 4999, 'duration_days' => 365, 'is_active' => true],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
