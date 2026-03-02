<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post)
    {
        abort_unless($post->published, 404);

        $validated = $request->validated();

        $post->comments()->create([
            'user_id' => $request->user()?->id,
            'name' => $validated['name'],
            'content' => $validated['content'],
        ]);

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', 'Comentario enviado com sucesso.');
    }
}
