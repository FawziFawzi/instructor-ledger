<?php

namespace App\Providers;

use App\Contracts\RevenueAllocationServiceInterface;
use App\Services\RevenueAllocationService;
use Illuminate\Support\ServiceProvider;

class BindServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(RevenueAllocationServiceInterface::class, RevenueAllocationService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
