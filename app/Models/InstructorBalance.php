<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorBalance extends Model
{
    /** @use HasFactory<\Database\Factories\InstructorBalanceFactory> */
    use HasFactory;
    protected $fillable = ['instructor_id', 'total_balance', 'pending_balance', 'available_balance'];
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
