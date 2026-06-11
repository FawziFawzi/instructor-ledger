<?php

namespace App\Contracts;

use App\Models\Subscription;

interface RevenueAllocationServiceInterface
{
    public function allocate(Subscription $subscription ): void;
}
