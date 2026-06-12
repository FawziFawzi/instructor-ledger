<?php

namespace App\Contracts;

interface MockPaymentGatewayServiceInterface
{
    public function transfer(string $idempotencyKey, int $amount): string;
    public function verifyTransfer(string $idempotencyKey): string;
}
