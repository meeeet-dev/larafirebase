<?php

namespace MeeeetDev\Larafirebase\Channels;

use Illuminate\Notifications\Notification;

class FirebaseChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return mixed
     * @throws \Exception
     */
    public function send($notifiable, Notification $notification)
    {
        // Check if the toFirebase method exists and is callable
        // Using both method_exists and is_callable for better trait support
        if (!method_exists($notification, 'toFirebase') && !is_callable([$notification, 'toFirebase'])) {
            $notificationClass = get_class($notification);
            $traits = class_uses_recursive($notification);
            $traitsList = !empty($traits) ? implode(', ', array_keys($traits)) : 'none';
            
            throw new \Exception(
                "The notification class '{$notificationClass}' is missing a toFirebase method. " .
                "Please implement the toFirebase(\$notifiable) method directly in your notification class " .
                "or use the HasFirebaseNotification trait. " .
                "Current traits used: {$traitsList}"
            );
        }

        try {
            /** @var \MeeeetDev\Larafirebase\Messages\FirebaseMessage $message */
            $message = $notification->toFirebase($notifiable);
            
            return $message;
        } catch (\Exception $e) {
            throw new \Exception(
                "Error calling toFirebase method on notification: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
