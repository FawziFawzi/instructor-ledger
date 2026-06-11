<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;

    protected $fillable = ['student_id', 'platform_fee_amount', 'amount', 'status', 'plan', 'start_date', 'end_date'];

    protected $casts = [
        'status' => SubscriptionStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function shares()
    {
        return $this->hasMany(SubscriptionInstructorShare::class);
    }
}
