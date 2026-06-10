<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;

    protected $fillable = ['student_id', 'platform_fee_amount', 'amount', 'status', 'plan', 'start_date', 'end_date'];
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function shares()
    {
        return $this->hasMany(SubscriptionInstructorShare::class);
    }
}
