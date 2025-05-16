<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Error>
 */
class ErrorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => \App\Models\Task::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'code_snippet' => $this->faker->sentence(5),
            'cause' => $this->faker->sentence(5),
            'resolution' =>  $this->faker->sentence(5),
            'severity' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['unresolved', 'resolved']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
