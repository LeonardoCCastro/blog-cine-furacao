<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    public function destroy(Post $post, Comment $comment)
    {
        abort_unless($comment->post_id === $post->id, 404);

        $comment->delete();

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Comentario removido com sucesso.');
    }
}
