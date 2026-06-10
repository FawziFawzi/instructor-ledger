<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionInstructorShare extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionInstructorShareFactory> */
    use HasFactory;

    protected $fillable = ['subscription_id', 'instructor_id', 'amount'];
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
