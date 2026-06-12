<?php

namespace App\Jobs;

use App\Contracts\MockPaymentGatewayServiceInterface;
use App\Models\InstructorBalance;
use App\Models\LedgerEntry;
use App\Models\Payout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessInstructorPayoutJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $instructorId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MockPaymentGatewayServiceInterface $service): void
    {
        //redis Lock
        $lock = \Cache::lock("payout:{$this->instructorId}", 60);
        if (!$lock->get())
            return;

        try {
            \DB::transaction(function () use ($service) {
                //Lock payment row for update to prevent race
                $balance = InstructorBalance::query()
                    ->where('instructor_id', $this->instructorId)
                    ->lockForUpdate()
                    ->first();
                if (!$balance || $balance->pending_balance <= 0)
                    return;

                //Create Payout
                $payout = Payout::create([
                    'instructor_id' => $this->instructorId,
                    'amount' => $balance->pending_balance,
                    'idempotency_key' => Str::uuid(),
                    'status' => 'pending',
                ]);

                //Gateway Transfer
                try {
                    $result = $service->transfer($payout->idempotency_key, $payout->amount);

                    if ($result === 'success') {
                        $payout->update(['status' => 'success']);
                        $balance->increment('available_balance', $payout->amount);
                        $balance->decrement('pending_balance', $payout->amount);

                        //Ledger Entry
                        LedgerEntry::create([
                            'instructor_id' => $this->instructorId,
                            'payout_id' => $payout->id,
                            'amount' => $payout->amount,
                            'description' => "Payout #{$payout->id} to instructor",
                        ]);
                    }

                    if ($result === 'failed') {
                        $payout->update(['status' => 'failed']);
                    }
                }catch (\Exception $e) {
                    //Gateway Timeout
                    $payout->update(['status' => 'pending_verification']);
                }
            });
        } finally {
            $lock->release();
        }
    }
}
