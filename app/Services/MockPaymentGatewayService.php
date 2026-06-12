<?php

namespace App\Services;

use App\Contracts\MockPaymentGatewayServiceInterface;

class MockPaymentGatewayService implements MockPaymentGatewayServiceInterface
{
    public function transfer(string $idempotencyKey, int $amount): string
    {
        $randomNumber = rand(1, 3);
        $status = match ($randomNumber) {
            1 => 'success',
            2 => 'failed',
            3 => 'Gateway timeout'
        };
        if ($randomNumber === 3)
            throw new \Exception('Gateway timeout');

        return $status;
    }

    public function verifyTransfer(string $idempotencyKey): string
    {
        //gateway lookup (Simulation)
        return rand(0, 1)
            ?'success'
            :'failed';
    }
}
