<?php

namespace MeeeetDev\Larafirebase\Examples;

use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Traits\HasFirebaseNotification;

/**
 * Example showing topic-based notification
 */
class TopicNotification extends Notification
{
    use HasFirebaseNotification;

    public $title = 'Breaking News';
    public $body;
    public $topic = 'news'; // Send to all users subscribed to 'news' topic
    public $data;

    public function __construct($newsTitle, $newsUrl)
    {
        $this->body = $newsTitle;
        $this->data = [
            'url' => $newsUrl,
            'action' => 'open_article'
        ];
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }

    // Override to return null since we're using topic
    protected function getFirebaseTokens($notifiable)
    {
        return null; // Not needed when using topic
    }
}
