<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = $this->postsQuery()
            ->with(['user:id,name', 'category:id,name', 'subcategory:id,name'])
            ->withCount('comments')
            ->latest()
            ->paginate(10)
            ->through(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'image_url' => $post->image_url,
                'category' => $post->category ? [
                    'id' => $post->category->id,
                    'name' => $post->category->name,
                ] : null,
                'subcategory' => $post->subcategory ? [
                    'id' => $post->subcategory->id,
                    'name' => $post->subcategory->name,
                ] : null,
                'published' => $post->published,
                'views' => $post->views,
                'comments_count' => $post->comments_count,
                'created_at' => $post->created_at?->toDateTimeString(),
                'user' => $post->user ? [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                ] : null,
            ]);

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Posts/Create', [
            'categories' => $this->categoriesForForm(),
        ]);
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('posts', 'public')
            : null;
        $subcategoryId = $this->resolveSubcategoryId($validated['category_id'], $validated['subcategory_id'] ?? null, $validated['new_subcategory_name'] ?? null);

        $request->user()->posts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'category_id' => $validated['category_id'],
            'subcategory_id' => $subcategoryId,
            'image' => $imagePath,
            'published' => $validated['published'] ?? false,
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post criado com sucesso.');
    }

    public function show(Post $post)
    {
        $this->authorizePostAccess($post);

        return redirect()->route('admin.posts.edit', $post);
    }

    public function edit(Post $post)
    {
        $this->authorizePostAccess($post);
        $post->load(['comments' => fn ($query) => $query->latest()]);

        return Inertia::render('Admin/Posts/Edit', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'excerpt' => $post->excerpt,
                'category_id' => $post->category_id,
                'subcategory_id' => $post->subcategory_id,
                'published' => $post->published,
                'image_url' => $post->image_url,
                'comments' => $post->comments->map(fn ($comment) => [
                    'id' => $comment->id,
                    'name' => $comment->name ?: 'Anonimo',
                    'content' => $comment->content,
                    'created_at' => $comment->created_at?->toDateTimeString(),
                ])->values(),
            ],
            'categories' => $this->categoriesForForm(),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorizePostAccess($post);
        $validated = $request->validated();
        $imagePath = $post->image;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $request->file('image')->store('posts', 'public');
        }
        $subcategoryId = $this->resolveSubcategoryId($validated['category_id'], $validated['subcategory_id'] ?? null, $validated['new_subcategory_name'] ?? null);

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'category_id' => $validated['category_id'],
            'subcategory_id' => $subcategoryId,
            'image' => $imagePath,
            'published' => $validated['published'] ?? false,
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post atualizado com sucesso.');
    }

    public function destroy(Post $post)
    {
        $this->authorizePostAccess($post);
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post removido com sucesso.');
    }

    private function categoriesForForm()
    {
        return Category::query()
            ->whereNull('parent_id')
            ->with('subcategories')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'subcategories' => $category->subcategories
                    ->sortBy('name')
                    ->values()
                    ->map(fn (Category $subcategory) => [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                    ]),
            ]);
    }

    private function postsQuery()
    {
        return Post::query()
            ->when(
                ! request()->user()->is_admin,
                fn ($query) => $query->where('user_id', request()->user()->id)
            );
    }

    private function authorizePostAccess(Post $post): void
    {
        abort_unless(request()->user()->canManagePost($post), 403);
    }

    private function resolveSubcategoryId(int $categoryId, ?int $subcategoryId, ?string $newSubcategoryName): ?int
    {
        $trimmedName = trim((string) $newSubcategoryName);

        if ($trimmedName !== '') {
            $slug = Str::slug($trimmedName);
            $existing = Category::query()
                ->where('parent_id', $categoryId)
                ->where('slug', $slug)
                ->first();

            if ($existing) {
                return $existing->id;
            }

            return Category::query()->create([
                'name' => $trimmedName,
                'slug' => $this->generateUniqueCategorySlug($trimmedName),
                'parent_id' => $categoryId,
            ])->id;
        }

        return $subcategoryId;
    }

    private function generateUniqueCategorySlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Category::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
