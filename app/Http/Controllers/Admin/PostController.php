<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->with('user:id,name')
            ->latest()
            ->paginate(10)
            ->through(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'image_url' => $post->image_url,
                'published' => $post->published,
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
        return Inertia::render('Admin/Posts/Create');
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('posts', 'public')
            : null;

        $request->user()->posts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'image' => $imagePath,
            'published' => $validated['published'] ?? false,
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post criado com sucesso.');
    }

    public function show(Post $post)
    {
        return redirect()->route('admin.posts.edit', $post);
    }

    public function edit(Post $post)
    {
        $post->load(['comments' => fn ($query) => $query->latest()]);

        return Inertia::render('Admin/Posts/Edit', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'excerpt' => $post->excerpt,
                'published' => $post->published,
                'image_url' => $post->image_url,
                'comments' => $post->comments->map(fn ($comment) => [
                    'id' => $comment->id,
                    'name' => $comment->name ?: 'Anonimo',
                    'content' => $comment->content,
                    'created_at' => $comment->created_at?->toDateTimeString(),
                ])->values(),
            ],
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $validated = $request->validated();
        $imagePath = $post->image;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'image' => $imagePath,
            'published' => $validated['published'] ?? false,
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post atualizado com sucesso.');
    }

    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post removido com sucesso.');
    }
}
