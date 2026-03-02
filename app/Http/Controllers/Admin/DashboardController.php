<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Admin/Dashboard', [
            'featuredPosts' => Post::query()
                ->where('published', true)
                ->latest()
                ->limit(6)
                ->get()
                ->map(fn (Post $post) => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'published' => $post->published,
                    'created_at' => $post->created_at?->toDateTimeString(),
                ]),
        ]);
    }
}
