<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;
    protected $oldStatus;
    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post, string $oldStatus, ?string $comment = null)
    {
        $this->post = $post;
        $this->oldStatus = $oldStatus;
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $statusName = ucfirst(str_replace('_', ' ', $this->post->status));
        $oldStatusName = ucfirst(str_replace('_', ' ', $this->oldStatus));

        $mail = (new MailMessage)
            ->subject('Kathaingo: Article Status Update / பதிவு நிலை மாற்றம்')
            ->greeting('வணக்கம் (Hello) ' . $notifiable->name . ',')
            ->line('Your article "' . $this->post->title . '" has been updated from status "' . $oldStatusName . '" to "' . $statusName . '".');

        if ($this->post->status === 'rejected' && $this->comment) {
            $mail->line('The reviewer requested revisions with the following feedback:')
                 ->line('"' . $this->comment . '"')
                 ->line('Please update your draft and submit it again.');
        }

        if ($this->post->status === 'published') {
            $mail->line('Your article is now live on our website!')
                 ->action('View Live Article / கட்டுரையைக் காண்க', route('posts.show', $this->post->slug));
        } else {
            $mail->action('Go to Dashboard / முகப்புப் பலகை', route('dashboard'));
        }

        return $mail->line('Thank you for contributing to Kathaingo / கவிதைஞோ குழுமம்!');
    }
}
