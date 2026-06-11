<?php

namespace App\Models;

use App\Enums\LedgerEntryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    /** @use HasFactory<\Database\Factories\LedgerEntryFactory> */
    use HasFactory;

    protected $fillable = ['instructor_id', 'subscription_id', 'payout_id', 'amount', 'type', 'description'];

    protected $casts = [
        'type' => LedgerEntryType::class,
    ];
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function payout()
    {
        return $this->belongsTo(Payout::class);
    }
}
