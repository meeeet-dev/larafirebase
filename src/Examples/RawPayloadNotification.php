<?php

namespace MeeeetDev\Larafirebase\Examples;

use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Traits\HasFirebaseNotification;

/**
 * Example showing raw payload customization
 */
class RawPayloadNotification extends Notification
{
    use HasFirebaseNotification;

    private $customPayload;

    public function __construct($customPayload)
    {
        $this->customPayload = $customPayload;
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }

    /**
     * Return raw Firebase payload for complete control
     */
    protected function getFirebaseRaw($notifiable): ?array
    {
        return [
            'message' => [
                'token' => $notifiable->fcm_token,
                'notification' => [
                    'title' => 'Custom Title',
                    'body' => 'Custom Body',
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'color' => '#FF0000',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
                'data' => $this->customPayload,
            ],
        ];
    }
}
