<?php

namespace App\Console\Commands;

use App\Contracts\MockPaymentGatewayServiceInterface;
use App\Models\InstructorBalance;
use App\Models\LedgerEntry;
use App\Models\Payout;
use Illuminate\Console\Command;

class ReconcilePendingPayoutsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reconcile-payouts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify payouts stuck in pending verification';

    /**
     * Execute the console command.
     */
    public function handle(MockPaymentGatewayServiceInterface $service)
    {
        Payout::query()
            ->where('status', 'pending_verification')
            ->chunkById(100, function ($payouts) use ($service){
                foreach ($payouts as $payout) {
                    $lockedPayout = Payout::query()
                        ->where('id', $payout->id)
                        ->lockForUpdate()
                        ->first();
                    if ($lockedPayout->status->value !== 'pending_verification'){
                        return;
                    }

                    //verify with gateway
                    $result = $service->verifyTransfer($lockedPayout->idempotency_key);
                    if ($result === 'success') {
                        $lockedPayout->update(['status' => 'success']);
                        $balance = InstructorBalance::query()
                            ->where('instructor_id', $lockedPayout->instructor_id)
                            ->lockForUpdate()
                            ->first();
                        if ($balance && $balance->pending_balance == $lockedPayout->amount)
                        {
                            $balance->decrement('pending_balance', $lockedPayout->amount);
                            $balance->increment('available_balance', $lockedPayout->amount);
                        }

                        //Create Ledger Entry
                        LedgerEntry::create([
                            'instructor_id' => $lockedPayout->instructor_id,
                            'payout_id' => $lockedPayout->id,
                            'amount' => $lockedPayout->amount,
                            'type' => 'payout',
                            'description' => "Reconciled Payout #{$lockedPayout->id} successfully",
                        ]);
                    }else{
                        $lockedPayout->update(['status' => 'failed']);
                    }
                }
            });
    }
}
