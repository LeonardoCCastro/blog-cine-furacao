<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function open(DatabaseNotification $notification)
    {
        abort_unless($notification->notifiable_id === request()->user()->id, 403);

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $postId = data_get($notification->data, 'post_id');
        $commentId = data_get($notification->data, 'comment_id');

        $response = redirect()->route('admin.posts.edit', [
            'post' => $postId,
        ]);

        return $commentId ? $response->withFragment("comment-{$commentId}") : $response;
    }

    public function readAll()
    {
        request()->user()?->unreadNotifications()->update([
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Notificacoes marcadas como lidas.');
    }
}
