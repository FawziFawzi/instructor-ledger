<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SubscriptionSeeder::class,
            SubscriptionInstructorShareSeeder::class,
            InstructorBalanceSeeder::class,
            PayoutSeeder::class,
            LedgerEntrySeeder::class,
        ]);
    }
}
