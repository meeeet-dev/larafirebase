<?php

namespace MeeeetDev\Larafirebase\Examples;

use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Traits\HasFirebaseNotification;

/**
 * Example notification using the HasFirebaseNotification trait
 * 
 * This demonstrates the simplest way to use the trait with property-based configuration.
 */
class ExampleNotification extends Notification
{
    use HasFirebaseNotification;

    public $title;
    public $body;
    public $image;
    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $body, $image = null, $data = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->image = $image;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['firebase'];
    }
}
