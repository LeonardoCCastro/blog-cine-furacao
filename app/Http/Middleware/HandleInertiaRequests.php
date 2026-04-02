<?php

namespace App\Http\Middleware;

use App\Models\Category;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'blogCategories' => fn () => Category::query()
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
                ]),
            'adminNotifications' => fn () => $request->user() && $request->user()->is_admin
                ? [
                    'unread_count' => $request->user()->unreadNotifications()->count(),
                    'items' => $request->user()->notifications()->latest()->limit(8)->get()->map(
                        fn ($notification) => [
                            'id' => $notification->id,
                            'read_at' => $notification->read_at?->toDateTimeString(),
                            'created_at' => $notification->created_at?->toDateTimeString(),
                            'data' => $notification->data,
                        ]
                    ),
                ]
                : null,
        ];
    }
}
