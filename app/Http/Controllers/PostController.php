<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('category', 'user')
            ->where('published', true)
            ->latest()
            ->get()
            ->map(fn($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'cover_image' => $post->cover_image,
                'created_at' => $post->created_at->toDateTimeString(),
                'user' => $post->user ? ['id'=>$post->user->id, 'name'=>$post->user->name] : null,
                'category' => $post->category ? ['id'=>$post->category->id, 'name'=>$post->category->name] : null,
            ]);

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    public function show(Post $post)
    {
        $post->load('category','user');

        return Inertia::render('Posts/Show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'cover_image' => $post->cover_image,
                'created_at' => $post->created_at->toDateTimeString(),
                'user' => $post->user ? ['id'=>$post->user->id,'name'=>$post->user->name] : null,
                'category' => $post->category ? ['id'=>$post->category->id,'name'=>$post->category->name] : null,
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
