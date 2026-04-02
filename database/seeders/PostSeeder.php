<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@blog.test')->firstOrFail();
        $writer = User::where('email', 'writer@blog.test')->first();
        $categories = $this->seedCategories();

        $posts = Post::factory()
            ->count(8)
            ->for($admin)
            ->state(new Sequence(
                ['published' => true],
                ['published' => true],
                ['published' => true],
                ['published' => false],
                ['published' => true],
                ['published' => true],
                ['published' => true],
                ['published' => true],
            ))
            ->create();

        if ($writer) {
            $posts = $posts->merge(
                Post::factory()
                    ->count(4)
                    ->for($writer)
                    ->state(new Sequence(
                        ['published' => true],
                        ['published' => false],
                        ['published' => true],
                        ['published' => true],
                    ))
                    ->create()
            );
        }

        foreach ($posts as $post) {
            $category = $categories->random();
            $subcategory = $category->subcategories->isNotEmpty() ? $category->subcategories->random() : null;
            $post->update([
                'category_id' => $category->id,
                'subcategory_id' => $subcategory?->id,
                'views' => random_int(10, 400),
            ]);
        }

        $reviewCategory = $categories->firstWhere('slug', 'review');

        if ($reviewCategory) {
            $reviewPosts = [
                [
                    'title' => 'Review: Nosferatu acerta no horror gotico e no peso visual',
                    'excerpt' => 'Uma analise do remake, destacando atmosfera, direcao e impacto do elenco principal.',
                    'content' => '<p>Review de teste da categoria Review com foco em fotografia, direcao e ritmo.</p>',
                    'published' => true,
                    'views' => 540,
                    'created_at' => now()->subDays(1),
                    'user_id' => $admin->id,
                ],
                [
                    'title' => 'Review: Duna Parte Dois entrega escala e tensao raras no genero',
                    'excerpt' => 'Post de teste para validar a nova categoria Review na home e nas listagens.',
                    'content' => '<p>Conteudo de teste da categoria Review com uma avaliacao positiva do filme.</p>',
                    'published' => true,
                    'views' => 490,
                    'created_at' => now()->subDays(3),
                    'user_id' => $writer?->id ?? $admin->id,
                ],
            ];

            foreach ($reviewPosts as $payload) {
                Post::query()->updateOrCreate(
                    ['title' => $payload['title']],
                    [
                        ...$payload,
                        'category_id' => $reviewCategory->id,
                        'updated_at' => $payload['created_at'],
                    ]
                );
            }
        }

        foreach (range(1, 10) as $index) {
            $post = $posts->random();

            Comment::factory()->create([
                'post_id' => $post->id,
                'name' => $index % 3 === 0 ? null : fake()->name(),
                'user_id' => null,
            ]);
        }
    }

    private function seedCategories()
    {
        $categories = collect([
            'Noticias',
            'Review',
            'Filmes',
            'Series',
            'Premiacoes',
        ])->map(function (string $name) {
            return Category::firstOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name, 'parent_id' => null]
            );
        });

        $subcategories = [
            'Noticias' => ['Bastidores', 'Bilheteria'],
            'Review' => ['Critica', 'Sem Spoilers'],
            'Filmes' => ['Analises', 'Bastidores'],
            'Series' => ['Episodios', 'Teorias'],
            'Premiacoes' => ['Oscar', 'Festivais'],
        ];

        foreach ($categories as $category) {
            foreach ($subcategories[$category->name] ?? [] as $subcategoryName) {
                Category::firstOrCreate(
                    ['slug' => str($subcategoryName.'-'.$category->name)->slug()],
                    [
                        'name' => $subcategoryName,
                        'parent_id' => $category->id,
                    ]
                );
            }
        }

        return Category::with('subcategories')
            ->whereNull('parent_id')
            ->get();
    }
}
