<?php

namespace Database\Seeders;

use App\Enums\LedgerEntryType;
use App\Models\LedgerEntry;
use App\Models\Payout;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class LedgerEntrySeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('type', 'instructor')->get();
        $subscriptions = Subscription::all();
        $payouts = Payout::all();

        foreach ($subscriptions as $subscription) {
            $instructor = $instructors->random();

            // Revenue entry
            LedgerEntry::factory()->revenue()->create([
                'instructor_id' => $instructor->id,
                'subscription_id' => $subscription->id,
                'amount' => $subscription->amount,
                'description' => "Revenue from subscription #{$subscription->id}",
            ]);

            // Platform fee entry
            LedgerEntry::factory()->platformFee()->create([
                'instructor_id' => $instructor->id,
                'subscription_id' => $subscription->id,
                'amount' => $subscription->platform_fee_amount,
                'description' => "Platform fee for subscription #{$subscription->id}",
            ]);
        }

        foreach ($payouts as $payout) {
            LedgerEntry::factory()->payout()->create([
                'instructor_id' => $payout->instructor_id,
                'payout_id' => $payout->id,
                'amount' => $payout->amount,
                'description' => "Payout #{$payout->id} to instructor",
            ]);
        }

        // A few refund entries
        $subscriptions->random(min(3, $subscriptions->count()))->each(function ($subscription) use ($instructors) {
            LedgerEntry::factory()->refund()->create([
                'instructor_id' => $instructors->random()->id,
                'subscription_id' => $subscription->id,
                'amount' => $subscription->amount,
                'description' => "Refund for subscription #{$subscription->id}",
            ]);
        });
    }
}
