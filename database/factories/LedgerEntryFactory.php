<?php

namespace Database\Factories;

use App\Enums\LedgerEntryType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LedgerEntry>
 */
class LedgerEntryFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(LedgerEntryType::cases());

        return [
            'instructor_id' => User::factory(),
            'subscription_id' => null,
            'payout_id' => null,
            'type' => $type,
            'amount' => fake()->randomFloat(2, 1, 300),
            'description' => fake()->optional()->sentence(),
        ];
    }

    public function revenue(): static
    {
        return $this->state(fn () => ['type' => LedgerEntryType::Revenue]);
    }

    public function platformFee(): static
    {
        return $this->state(fn () => ['type' => LedgerEntryType::PlatformFee]);
    }

    public function refund(): static
    {
        return $this->state(fn () => ['type' => LedgerEntryType::Refund]);
    }

    public function payout(): static
    {
        return $this->state(fn () => ['type' => LedgerEntryType::Payout]);
    }
}
