<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    public function definition(): array
    {
        $userId = fake()->boolean(35) ? User::factory() : null;

        return [
            'post_id' => Post::factory(),
            'user_id' => $userId,
            'name' => $userId ? null : fake()->name(),
            'content' => fake()->paragraph(),
        ];
    }
}
