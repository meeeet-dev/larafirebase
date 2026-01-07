<?php

namespace MeeeetDev\Larafirebase\Examples;

use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Traits\HasFirebaseNotification;
use MeeeetDev\Larafirebase\Config\AndroidConfig;
use MeeeetDev\Larafirebase\Config\AndroidNotification;
use MeeeetDev\Larafirebase\Config\ApnsConfig;
use MeeeetDev\Larafirebase\Config\WebpushConfig;

/**
 * Example notification with platform-specific configurations
 * 
 * This demonstrates how to customize notifications for each platform
 * using the platform configuration classes.
 */
class PlatformSpecificNotification extends Notification
{
    use HasFirebaseNotification;

    private $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }

    /**
     * Customize for Android
     */
    protected function getAndroidConfig($notifiable): ?AndroidConfig
    {
        $androidNotification = (new AndroidNotification)
            ->withTitle('Order Confirmed')
            ->withBody("Your order #{$this->order['id']} is confirmed!")
            ->withColor('#4CAF50')
            ->withSound('order_sound')
            ->withChannelId('orders')
            ->withClickAction('OPEN_ORDER_ACTIVITY')
            ->withNotificationPriority('PRIORITY_HIGH')
            ->withLightSettings('#4CAF50', 1000, 500);

        return (new AndroidConfig)
            ->withPriority('high')
            ->withTtl(86400) // 24 hours
            ->withNotification($androidNotification);
    }

    /**
     * Customize for iOS
     */
    protected function getApnsConfig($notifiable): ?ApnsConfig
    {
        $apsPayload = [
            'alert' => [
                'title' => 'Order Confirmed',
                'body' => "Your order #{$this->order['id']} is confirmed!",
            ],
            'badge' => 1,
            'sound' => 'default',
            'category' => 'ORDER_CATEGORY',
        ];

        return (new ApnsConfig)
            ->withPriority(10) // High priority
            ->withApsPayload($apsPayload)
            ->withAnalyticsLabel('order_confirmation');
    }

    /**
     * Customize for Web
     */
    protected function getWebpushConfig($notifiable): ?WebpushConfig
    {
        $webNotification = [
            'title' => 'Order Confirmed',
            'body' => "Your order #{$this->order['id']} is confirmed!",
            'icon' => 'https://example.com/icon.png',
            'badge' => 'https://example.com/badge.png',
            'image' => 'https://example.com/order-image.png',
        ];

        return (new WebpushConfig)
            ->withNotification($webNotification)
            ->withLink("https://example.com/orders/{$this->order['id']}")
            ->withUrgency('high')
            ->withTtl(86400);
    }

    /**
     * Common notification data
     */
    protected function getFirebaseTitle($notifiable): string
    {
        return 'Order Confirmed';
    }

    protected function getFirebaseBody($notifiable): string
    {
        return "Your order #{$this->order['id']} is confirmed!";
    }

    protected function getFirebaseData($notifiable): ?array
    {
        return [
            'order_id' => $this->order['id'],
            'order_total' => $this->order['total'],
            'action' => 'view_order',
        ];
    }

    protected function getAnalyticsLabel($notifiable): ?string
    {
        return 'order_confirmation_campaign';
    }
}
