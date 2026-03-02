<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\CommentController;
use App\Http\Controllers\Public\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts', [PostController::class, 'all'])->name('posts.all');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::resource('posts.comments', CommentController::class)->only('store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return request()->user()->is_admin
            ? redirect('/admin')
            : redirect('/');
    })->name('dashboard');
});

Route::prefix('admin')
    ->as('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('posts', AdminPostController::class);
        Route::delete('posts/{post}/comments/{comment}', [AdminCommentController::class, 'destroy'])
            ->name('posts.comments.destroy');
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
