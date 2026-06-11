<?php

namespace App\Enums;

enum LedgerEntryType: string
{
    case Revenue = 'revenue';
    case PlatformFee = 'platform_fee';
    case Refund = 'refund';
    case Payout = 'payout';
}
