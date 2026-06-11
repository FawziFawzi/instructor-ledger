<?php

namespace App\Enums;

enum PayoutStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Success = 'success';
    case Failed = 'failed';
    case PendingVerification = 'pending_verification';
}
