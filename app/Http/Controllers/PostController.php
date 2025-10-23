<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('category', 'user')
            ->where('published', true)
            ->latest()
            ->take(4)
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

    public function all()
    {
        $posts = Post::with('category', 'user')
            ->where('published', true)
            ->latest()
            ->paginate(3)
            ->through(fn($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'cover_image' => $post->cover_image,
                'created_at' => $post->created_at->toDateTimeString(),
                'user' => $post->user ? ['id'=>$post->user->id, 'name'=>$post->user->name] : null,
                'category' => $post->category ? ['id'=>$post->category->id, 'name'=>$post->category->name] : null,
            ]);

        return Inertia::render('Posts/All', [
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
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'excerpt' => 'nullable|string|max:500',
                'content' => 'required|string',
                'cover_image' => 'nullable|image|max:2048',
            ]);

            $path = null;

            if ($request->hasFile('cover_image')) {
                $path = $request->file('cover_image')->store('posts', 'public');
            }

            $request->user()->posts()->create([
                'title' => $data['title'],
                'excerpt' => $data['excerpt'] ?? null,
                'content' => $data['content'],
                'cover_image' => $path,
            ]);

            return back()->with('success', 'Post criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar post: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'user_id' => $request->user()->id ?? null,
            ]);

            return back()->with('error', 'Ocorreu um erro ao criar o post. Tente novamente mais tarde.');
        }
    }

}
