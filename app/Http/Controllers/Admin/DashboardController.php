<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $postsQuery = Post::query()
            ->when(
                ! request()->user()->is_admin,
                fn ($query) => $query->where('user_id', request()->user()->id)
            );
        $visiblePostIds = (clone $postsQuery)->pluck('id');
        $totalPosts = (clone $postsQuery)->count();
        $publishedPosts = (clone $postsQuery)->where('published', true)->count();
        $draftPosts = (clone $postsQuery)->where('published', false)->count();
        $totalViews = (clone $postsQuery)->sum('views');
        $totalComments = Comment::query()->whereIn('post_id', $visiblePostIds)->count();
        $recentActivity = (clone $postsQuery)
            ->withCount('comments')
            ->latest()
            ->limit(7)
            ->get()
            ->reverse()
            ->values();
        $topByViews = (clone $postsQuery)
            ->withCount('comments')
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'total_posts' => $totalPosts,
                'published_posts' => $publishedPosts,
                'draft_posts' => $draftPosts,
                'total_views' => $totalViews,
                'total_comments' => $totalComments,
                'average_views_per_post' => $totalPosts > 0 ? round($totalViews / $totalPosts, 1) : 0,
                'average_comments_per_post' => $totalPosts > 0 ? round($totalComments / $totalPosts, 1) : 0,
                'publication_rate' => $totalPosts > 0 ? round(($publishedPosts / $totalPosts) * 100, 1) : 0,
                'categories_count' => Category::query()->whereNull('parent_id')->count(),
                'tags_count' => Category::query()->whereNotNull('parent_id')->count(),
            ],
            'featuredPosts' => (clone $postsQuery)
                ->withCount('comments')
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn (Post $post) => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'published' => $post->published,
                    'views' => $post->views,
                    'comments_count' => $post->comments_count,
                    'created_at' => $post->created_at?->toDateTimeString(),
                ]),
            'topByViews' => $topByViews
                ->map(fn (Post $post) => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'views' => $post->views,
                    'comments_count' => $post->comments_count,
                    'published' => $post->published,
                ]),
            'activitySeries' => $recentActivity->map(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'short_title' => str($post->title)->limit(18)->value(),
                'views' => $post->views,
                'comments_count' => $post->comments_count,
                'created_at' => $post->created_at?->toDateTimeString(),
            ]),
            'topCategories' => Category::query()
                ->whereNull('parent_id')
                ->withCount('posts')
                ->orderByDesc('posts_count')
                ->orderBy('name')
                ->limit(5)
                ->get()
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'posts_count' => $category->posts_count,
                ]),
        ]);
    }
}
