<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->unique()->sentence(6),
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(6, true),
            'image' => null,
            'cover_image' => null,
            'published' => fake()->boolean(80),
        ];
    }
}
