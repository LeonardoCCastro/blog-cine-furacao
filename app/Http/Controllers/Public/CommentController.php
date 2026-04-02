<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewPostCommentNotification;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post)
    {
        abort_unless($post->published, 404);

        $validated = $request->validated();

        $comment = $post->comments()->create([
            'user_id' => $request->user()?->id,
            'name' => $validated['name'],
            'content' => $validated['content'],
        ]);

        $admins = User::query()
            ->where('is_admin', true)
            ->get();

        $authorName = $validated['name'] ?: $request->user()?->name ?: 'Anonimo';
        $preview = mb_substr($validated['content'], 0, 120);

        foreach ($admins as $admin) {
            $admin->notify(new NewPostCommentNotification(
                postId: $post->id,
                postTitle: $post->title,
                commentId: $comment->id,
                commentAuthor: $authorName,
                commentPreview: $preview,
            ));
        }

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', 'Comentario enviado com sucesso.');
    }
}
