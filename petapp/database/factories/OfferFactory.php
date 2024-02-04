<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-2 years', 'now');
        $userCreatedAt = (clone $createdAt)->modify('-' . rand(1, 720) . ' hours');

        return [
            'user_id' => User::factory()->create(['created_at' => $userCreatedAt, 'updated_at' => $userCreatedAt])->id,
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 500),
            'end_date' => Carbon::now()->addWeeks(3),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
