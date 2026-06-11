<?php

namespace Database\Seeders;

use App\Enums\PayoutStatus;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Database\Seeder;

class PayoutSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('type', 'instructor')->get();

        foreach ($instructors as $instructor) {
            Payout::factory(rand(1, 3))->create([
                'instructor_id' => $instructor->id,
            ]);
        }
    }
}
