<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
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

    public function create()
    {
        return Inertia::render('Posts/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $path = null;

        if($request->hasFile('image')){
            $path = $request->file('image')->store('posts','public');
        }

        $request->user()->posts()->create([
            'title' => $data['title'],
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'cover_image' => $path,
        ]);

        return redirect()->route('dashboard')->with('success', 'Post criado com sucesso!');
    }
}
