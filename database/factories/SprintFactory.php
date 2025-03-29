<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sprint>
 */
class SprintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'duration' => $this->faker->numberBetween(1, 4),
            'description' => $this->faker->sentence(10),
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'active' => $this->faker->boolean(),
        ];
    }
}
