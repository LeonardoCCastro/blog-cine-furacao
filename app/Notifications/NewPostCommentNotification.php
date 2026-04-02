<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPostCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int $postId,
        private readonly string $postTitle,
        private readonly int $commentId,
        private readonly string $commentAuthor,
        private readonly string $commentPreview,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->postId,
            'post_title' => $this->postTitle,
            'comment_id' => $this->commentId,
            'comment_author' => $this->commentAuthor,
            'comment_preview' => $this->commentPreview,
        ];
    }
}
