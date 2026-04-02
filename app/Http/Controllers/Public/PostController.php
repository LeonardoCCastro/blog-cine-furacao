<?php

namespace App\Http\Controllers\Public;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $weeklyTop = $this->topPostsWithinWindow(now()->subWeek(), 5);
        $trending = $this->topPostsWithinWindow(today(), 5);
        $latestPosts = $this->basePublishedPostsQuery()
            ->latest()
            ->limit(12)
            ->get()
            ->map(fn (Post $post) => $this->serializePost($post));
        $categorySections = Category::query()
            ->whereNull('parent_id')
            ->orderByRaw("case when slug = 'noticias' then 0 when slug = 'review' then 1 else 2 end")
            ->orderBy('name')
            ->get()
            ->map(function (Category $category) {
                $posts = $this->basePublishedPostsQuery()
                    ->where('category_id', $category->id)
                    ->latest()
                    ->limit(4)
                    ->get()
                    ->map(fn (Post $post) => $this->serializePost($post));

                if ($posts->isEmpty()) {
                    return null;
                }

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'posts' => $posts->values(),
                ];
            })
            ->filter()
            ->values();

        return Inertia::render('Posts/Index', [
            'weeklyTop' => $weeklyTop,
            'latestPosts' => $latestPosts,
            'categorySections' => $categorySections,
            'trendingPosts' => $trending,
        ]);
    }

    public function all(Request $request)
    {
        $search = $request->string('search')->toString();
        $categorySlug = $request->string('category')->toString();
        $subcategorySlug = $request->string('subcategory')->toString();

        $selectedCategory = $categorySlug !== ''
            ? Category::query()->whereNull('parent_id')->where('slug', $categorySlug)->first()
            : null;
        $selectedSubcategory = $subcategorySlug !== ''
            ? Category::query()->whereNotNull('parent_id')->where('slug', $subcategorySlug)->first()
            : null;

        $posts = Post::query()
            ->with(['user', 'category', 'subcategory'])
            ->where('published', true)
            ->when($selectedCategory, fn ($query) => $query->where('category_id', $selectedCategory->id))
            ->when(
                $selectedSubcategory && (!$selectedCategory || $selectedSubcategory->parent_id === $selectedCategory->id),
                fn ($query) => $query->where('subcategory_id', $selectedSubcategory->id)
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($postQuery) use ($search) {
                    $postQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->through(fn (Post $post) => $this->serializePost($post))
            ->withQueryString();

        return Inertia::render('Posts/All', [
            'posts' => $posts,
            'categories' => $this->categoriesForFilters(),
            'trendingPosts' => $this->topPostsWithinWindow(today(), 5),
            'filters' => [
                'search' => $search,
                'category' => $categorySlug !== '' ? $categorySlug : null,
                'subcategory' => $subcategorySlug !== '' ? $subcategorySlug : null,
            ],
        ]);
    }

    public function show(Post $post)
    {
        abort_unless($post->published, 404);

        $viewedKey = "viewed_post_{$post->id}";
        if (!session()->has($viewedKey)) {
            $post->increment('views');
            session()->put($viewedKey, true);
        }

        $post->load([
            'user:id,name',
            'category:id,name,slug',
            'subcategory:id,name,slug,parent_id',
            'comments' => fn ($query) => $query->latest()->with('user:id,name'),
        ]);

        return Inertia::render('Posts/Show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'image_url' => $post->image_url,
                'views' => $post->views,
                'category' => $post->category ? [
                    'id' => $post->category->id,
                    'name' => $post->category->name,
                    'slug' => $post->category->slug,
                ] : null,
                'subcategory' => $post->subcategory ? [
                    'id' => $post->subcategory->id,
                    'name' => $post->subcategory->name,
                    'slug' => $post->subcategory->slug,
                ] : null,
                'created_at' => $post->created_at?->toDateTimeString(),
                'user' => $post->user ? [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                ] : null,
                'comments' => $post->comments->map(fn ($comment) => [
                    'id' => $comment->id,
                    'name' => $comment->name ?: $comment->user?->name ?: 'Anonimo',
                    'content' => $comment->content,
                    'created_at' => $comment->created_at?->toDateTimeString(),
                ])->values(),
            ],
            'relatedPosts' => $this->relatedPosts($post),
            'trendingPosts' => $this->topPostsWithinWindow(today(), 5)
                ->reject(fn (array $relatedPost) => $relatedPost['id'] === $post->id)
                ->take(4)
                ->values(),
        ]);
    }

    private function categoriesForFilters()
    {
        return Category::query()
            ->whereNull('parent_id')
            ->with('subcategories')
            ->orderByRaw("case when slug = 'noticias' then 0 when slug = 'review' then 1 else 2 end")
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'subcategories' => $category->subcategories
                    ->sortBy('name')
                    ->values()
                    ->map(fn (Category $subcategory) => [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                        'slug' => $subcategory->slug,
                    ]),
            ]);
    }

    private function basePublishedPostsQuery()
    {
        return Post::query()
            ->with(['user:id,name', 'category:id,name,slug', 'subcategory:id,name,slug'])
            ->where('published', true);
    }

    private function topPostsWithinWindow($startDate, int $limit)
    {
        $windowPosts = $this->basePublishedPostsQuery()
            ->where('created_at', '>=', $startDate)
            ->orderByDesc('views')
            ->limit($limit)
            ->get();

        if ($windowPosts->count() < $limit) {
            $missing = $limit - $windowPosts->count();
            $fallbackPosts = $this->basePublishedPostsQuery()
                ->whereNotIn('id', $windowPosts->pluck('id'))
                ->orderByDesc('views')
                ->limit($missing)
                ->get();

            $windowPosts = $windowPosts->concat($fallbackPosts);
        }

        return $windowPosts->map(fn (Post $post) => $this->serializePost($post))->values();
    }

    private function serializePost(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'image_url' => $post->image_url,
            'views' => $post->views,
            'category' => $post->category ? [
                'id' => $post->category->id,
                'name' => $post->category->name,
                'slug' => $post->category->slug,
            ] : null,
            'subcategory' => $post->subcategory ? [
                'id' => $post->subcategory->id,
                'name' => $post->subcategory->name,
                'slug' => $post->subcategory->slug,
            ] : null,
            'created_at' => $post->created_at?->toDateTimeString(),
            'user' => $post->user ? [
                'id' => $post->user->id,
                'name' => $post->user->name,
            ] : null,
        ];
    }

    private function relatedPosts(Post $post)
    {
        $related = $this->basePublishedPostsQuery()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($query) => $query->where('category_id', $post->category_id))
            ->latest()
            ->limit(4)
            ->get();

        if ($related->count() < 4) {
            $related = $related->concat(
                $this->basePublishedPostsQuery()
                    ->where('id', '!=', $post->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->latest()
                    ->limit(4 - $related->count())
                    ->get()
            );
        }

        return $related
            ->map(fn (Post $relatedPost) => $this->serializePost($relatedPost))
            ->values();
    }
}
