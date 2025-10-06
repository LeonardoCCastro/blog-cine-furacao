<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;

class PostSeeder extends Seeder
{
    public function run()
    {
        // garante um usuário admin
        $user = User::first() ?? User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@blog.test',
            'password' => bcrypt('password'),
        ]);

        // categoria
        $category = Category::firstOrCreate(
            ['slug' => 'noticias'],
            ['name' => 'Notícias']
        );

        // post de exemplo
        Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Post de Boas-vindas',
            'slug' => 'post-de-boas-vindas',
            'excerpt' => 'Esse é o primeiro post do blog (gerado pelo seeder).',
            'content' => '<p>Conteúdo inicial de exemplo criado pelo seeder para testes.</p>',
            'published' => true,
        ]);
    }
}