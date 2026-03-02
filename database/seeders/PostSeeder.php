<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@blog.test')->firstOrFail();

        $posts = Post::factory()
            ->count(5)
            ->for($admin)
            ->state(new Sequence(
                ['published' => true],
                ['published' => true],
                ['published' => true],
                ['published' => false],
                ['published' => true],
            ))
            ->create();

        foreach (range(1, 10) as $index) {
            $post = $posts->random();

            Comment::factory()->create([
                'post_id' => $post->id,
                'name' => $index % 3 === 0 ? null : fake()->name(),
                'user_id' => null,
            ]);
        }
    }
}
