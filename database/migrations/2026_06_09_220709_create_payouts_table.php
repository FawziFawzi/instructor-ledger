<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('idempotency_key')->unique();
            $table->unsignedBigInteger('amount');
            $table->enum('status', [
                'pending',
                'processing',
                'success',
                'failed',
                'pending_verification'
            ])->default('pending');
            $table->timestamps();

            $table->index( 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
