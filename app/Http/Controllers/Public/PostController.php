<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->with('user')
            ->where('published', true)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'image_url' => $post->image_url,
                'created_at' => $post->created_at?->toDateTimeString(),
                'user' => $post->user ? [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                ] : null,
            ]);

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function all(Request $request)
    {
        $search = $request->string('search')->toString();

        $posts = Post::query()
            ->with('user')
            ->where('published', true)
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
            ->through(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'image_url' => $post->image_url,
                'created_at' => $post->created_at?->toDateTimeString(),
                'user' => $post->user ? [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                ] : null,
            ])
            ->withQueryString();

        return Inertia::render('Posts/All', [
            'posts' => $posts,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function show(Post $post)
    {
        abort_unless($post->published, 404);

        $post->load([
            'user:id,name',
            'comments' => fn ($query) => $query->latest()->with('user:id,name'),
        ]);

        return Inertia::render('Posts/Show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'image_url' => $post->image_url,
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
        ]);
    }
}
