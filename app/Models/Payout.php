<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    /** @use HasFactory<\Database\Factories\PayoutFactory> */
    use HasFactory;

    protected $fillable = ['instructor_id', 'amount', 'status', 'idempotency_key'];
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
