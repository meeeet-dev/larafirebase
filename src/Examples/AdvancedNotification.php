<?php

namespace MeeeetDev\Larafirebase\Examples;

use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Traits\HasFirebaseNotification;

/**
 * Advanced example notification with custom method overrides
 * 
 * This demonstrates how to override the trait methods for more control.
 */
class AdvancedNotification extends Notification
{
    use HasFirebaseNotification;

    private $user;
    private $orderDetails;

    public function __construct($user, $orderDetails)
    {
        $this->user = $user;
        $this->orderDetails = $orderDetails;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['firebase'];
    }

    /**
     * Customize the Firebase title
     */
    protected function getFirebaseTitle($notifiable): string
    {
        return "Hi {$this->user->name}!";
    }

    /**
     * Customize the Firebase body
     */
    protected function getFirebaseBody($notifiable): string
    {
        return "Your order #{$this->orderDetails['order_id']} has been confirmed!";
    }

    /**
     * Add custom data
     */
    protected function getFirebaseData($notifiable): ?array
    {
        return [
            'order_id' => $this->orderDetails['order_id'],
            'total' => $this->orderDetails['total'],
            'status' => 'confirmed',
            'action' => 'view_order'
        ];
    }

    /**
     * Customize token retrieval
     */
    protected function getFirebaseTokens($notifiable)
    {
        // Get all active device tokens for the user
        return $notifiable->deviceTokens()
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();
    }
}
