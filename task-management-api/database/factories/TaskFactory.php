<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "team_id" => 1,
            "title" => $this->faker->sentence,
            "description" => $this->faker->paragraph,
            "status" => $this->faker->randomElement(['todo', 'in_progress', 'done']),
            "created_by" => 1,
        ];
    }
}
