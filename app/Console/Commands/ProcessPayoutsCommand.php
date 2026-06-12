<?php

namespace App\Console\Commands;

use App\Jobs\ProcessInstructorPayoutJob;
use App\Models\InstructorBalance;
use Illuminate\Console\Command;

class ProcessPayoutsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-payouts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process payouts for instructors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        InstructorBalance::query()
            ->where('pending_balance', '>', 0)
            ->chunkById(100, function ($balances) {
                foreach ($balances as $balance) {
                    ProcessInstructorPayoutJob::dispatch($balance->instructor_id);
                }
            });
    }
}
