<?php

namespace App\Services;

use App\Contracts\RevenueAllocationServiceInterface;
use App\Models\InstructorBalance;
use App\Models\LedgerEntry;
use App\Models\Subscription;
use App\Models\SubscriptionInstructorShare;

class RevenueAllocationService implements RevenueAllocationServiceInterface
{
    private $platformFeePercentage = 20;
    public function allocate(Subscription $subscription): void
    {
        \DB::transaction(function () use ($subscription) {
            $shares = $subscription->shares()
            ->with('instructor')->get();

            if ($shares->isEmpty())
                throw new \Exception( 'No instructor shares found.' );

            $totalPercentage = $shares->sum( 'instructor_percentage' );
            if ((float) $totalPercentage !== 100.0)
                throw new \Exception( 'Instructor percentages must equal 100%' );

            $subscriptionAmount = $subscription->amount;

            $platformFee = (int) floor(
                $subscriptionAmount * (
                    $this->platformFeePercentage / 100
                )
            );
            $subscription->update([
                'platform_fee_amount' => $platformFee,
            ]);

            $remainingRevenue = $subscriptionAmount - $platformFee;


            // Allocate Revenue
            $allocatedAmount = 0;
            foreach ($shares as $index => $share) {

                //calculate share
                $shareAmount = (int) floor(
                    $remainingRevenue * ( $share->instructor_percentage / 100 )
                );
                $allocatedAmount += $shareAmount;

                //handle remaining cents
                if ($index === $shares->keys()->last()){
                    $difference = $remainingRevenue - $allocatedAmount;
                    $shareAmount += $difference;
                }
                //save final amount
                $share->update([
                    'amount' => $shareAmount,
                ]);

                $this->updateInstructorBalance($share, $shareAmount);

                $this->createRevenueLedgerEntry($share, $subscription, $shareAmount);

            }

            $this->createPlatformLedgerEntry($subscription, $platformFee);
        });
    }

    function createRevenueLedgerEntry($share, Subscription $subscription, $shareAmount)
    {
        LedgerEntry::create([
            'instructor_id' => $share->instructor_id,
            'subscription_id' => $subscription->id,
            'type' => 'revenue',
            'amount' => $shareAmount,
            'description' => "Revenue from subscription #{$subscription->id}",
        ]);
    }

    function updateInstructorBalance($share, $shareAmount)
    {
        $balance = InstructorBalance::firstOrCreate([
            'instructor_id' => $share->instructor_id,
        ]);
        $balance->increment('pending_balance', $shareAmount);
        $balance->increment('total_balance', $shareAmount);
    }

    /**
     * @param Subscription $subscription
     * @param int $platformFee
     * @return void
     */
    function createPlatformLedgerEntry(Subscription $subscription, $platformFee)
    {
        LedgerEntry::create([
            'subscription_id' => $subscription->id,
            'type' => 'platform_fee',
            'amount' => $platformFee,
            'description' => "Platform fee collected",
        ]);
    }
}
