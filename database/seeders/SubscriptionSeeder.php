<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('type', 'student')->get();
        $planIds = SubscriptionPlan::pluck('id');

        foreach ($students as $student) {
            Subscription::factory(rand(1, 3))->create([
                'student_id'           => $student->id,
                'subscription_plan_id' => $planIds->random(),
            ]);
        }
    }
}
