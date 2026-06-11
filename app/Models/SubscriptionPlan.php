<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionPlanFactory> */
    use HasFactory;
    protected $fillable = ['name', 'slug', 'price', 'duration_days', 'is_active'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'duration_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
