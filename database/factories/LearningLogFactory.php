<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LearningLog>
 */
class LearningLogFactory extends Factory
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
            'topic' => $this->faker->word(),
            'summary' => $this->faker->paragraph(),
            'duration' => $this->faker->numberBetween(1, 8),
            'resources' => [
                $this->faker->url(),
                $this->faker->url(),
                $this->faker->url(),
            ],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
