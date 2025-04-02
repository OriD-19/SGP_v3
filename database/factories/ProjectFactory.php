<?php

namespace Database\Factories;
use App\Models\Organization;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'organization_id' => Organization::factory(),
        ];
    }
}
