<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\SubscriptionInstructorShare;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionInstructorShareSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('type', 'instructor')->get();
        $subscriptions = Subscription::all();

        foreach ($subscriptions as $subscription) {
            $assignedInstructors = $instructors->random(rand(1, min(2, $instructors->count())));
            $shareAmount = round($subscription->amount / $assignedInstructors->count(), 2);

            foreach ($assignedInstructors as $instructor) {
                SubscriptionInstructorShare::factory()->create([
                    'subscription_id' => $subscription->id,
                    'instructor_id' => $instructor->id,
                    'amount' => $shareAmount,
                ]);
            }
        }
    }
}
