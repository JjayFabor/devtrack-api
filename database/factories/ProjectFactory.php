<?php

namespace Database\Factories;

use App\Models\User;
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
            'user_id' => User::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'tags' => json_encode([$this->faker->word(), $this->faker->word()]),
            'github_url' => $this->faker->url(),
            'status' => $this->faker->randomElement(['planning', 'active', 'paused', 'completed']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
