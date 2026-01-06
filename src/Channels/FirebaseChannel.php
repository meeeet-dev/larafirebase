<?php

namespace MeeeetDev\Larafirebase\Channels;

class FirebaseChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, $notification)
    {
        if (method_exists($notification, 'toFirebase')) {
            /** @var \MeeeetDev\Larafirebase\FirebaseMessage $message */
            $message = $notification->toFirebase($notifiable);
        } else {
            throw new \Exception('The notification is missing a toFirebase method.');
        }
    }
}
