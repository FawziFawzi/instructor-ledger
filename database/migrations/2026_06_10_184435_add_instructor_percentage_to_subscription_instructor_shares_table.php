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
        Schema::table('subscription_instructor_shares', function (Blueprint $table) {
            $table->decimal('instructor_percentage', 5, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_instructor_shares', function (Blueprint $table) {
            $table->dropColumn('instructor_percentage');
        });
    }
};
