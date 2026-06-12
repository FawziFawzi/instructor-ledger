<?php

namespace App\Providers;

use App\Contracts\MockPaymentGatewayServiceInterface;
use App\Contracts\RevenueAllocationServiceInterface;
use App\Services\MockPaymentGatewayService;
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
        $this->app->bind(MockPaymentGatewayServiceInterface::class, MockPaymentGatewayService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
