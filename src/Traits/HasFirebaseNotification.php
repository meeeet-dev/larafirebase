<?php

namespace MeeeetDev\Larafirebase\Traits;

use MeeeetDev\Larafirebase\Messages\FirebaseMessage;

/**
 * Trait HasFirebaseNotification
 * 
 * This trait provides a reusable implementation for Firebase notifications.
 * Use this trait in your notification classes to easily implement Firebase messaging.
 * 
 * @package MeeeetDev\Larafirebase\Traits
 */
trait HasFirebaseNotification
{
    /**
     * Get the Firebase representation of the notification.
     * 
     * Override this method in your notification class to customize the message.
     * You can also override individual getter methods for more granular control.
     *
     * @param mixed $notifiable
     * @return \MeeeetDev\Larafirebase\Messages\FirebaseMessage
     */
    public function toFirebase($notifiable)
    {
        // Check if using raw payload
        if ($raw = $this->getFirebaseRaw($notifiable)) {
            return (new FirebaseMessage)
                ->fromRaw($raw)
                ->send();
        }

        // Check if using array payload
        if ($array = $this->getFirebaseArray($notifiable)) {
            return (new FirebaseMessage)
                ->fromArray($array)
                ->send();
        }

        // Build standard message
        $message = (new FirebaseMessage)
            ->withTitle($this->getFirebaseTitle($notifiable))
            ->withBody($this->getFirebaseBody($notifiable));

        // Add optional fields
        if ($image = $this->getFirebaseImage($notifiable)) {
            $message->withImage($image);
        }

        if ($data = $this->getFirebaseData($notifiable)) {
            $message->withAdditionalData($data);
        }

        if ($topic = $this->getFirebaseTopic($notifiable)) {
            $message->withTopic($topic);
        }

        // Add platform-specific configurations
        if ($androidConfig = $this->getAndroidConfig($notifiable)) {
            $message->withAndroidConfig($androidConfig);
        }

        if ($apnsConfig = $this->getApnsConfig($notifiable)) {
            $message->withApnsConfig($apnsConfig);
        }

        if ($webpushConfig = $this->getWebpushConfig($notifiable)) {
            $message->withWebpushConfig($webpushConfig);
        }

        if ($analyticsLabel = $this->getAnalyticsLabel($notifiable)) {
            $message->withAnalyticsLabel($analyticsLabel);
        }

        if ($condition = $this->getCondition($notifiable)) {
            $message->withCondition($condition);
        }

        // Get tokens
        $tokens = $this->getFirebaseTokens($notifiable);

        // Determine delivery method (notification vs message)
        $deliveryMethod = $this->getFirebaseDeliveryMethod($notifiable);

        return $deliveryMethod === 'message' 
            ? $message->asMessage($tokens)
            : $message->asNotification($tokens);
    }

    /**
     * Get the notification title for Firebase.
     * Override this method to customize the title.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function getFirebaseTitle($notifiable): string
    {
        return property_exists($this, 'title') ? $this->title : 'Notification';
    }

    /**
     * Get the notification body for Firebase.
     * Override this method to customize the body.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function getFirebaseBody($notifiable): string
    {
        return property_exists($this, 'body') ? $this->body : 'You have a new notification';
    }

    /**
     * Get the notification image URL for Firebase.
     * Override this method to add an image.
     *
     * @param mixed $notifiable
     * @return string|null
     */
    protected function getFirebaseImage($notifiable): ?string
    {
        return property_exists($this, 'image') ? $this->image : null;
    }

    /**
     * Get additional data for Firebase.
     * Override this method to add custom data.
     *
     * @param mixed $notifiable
     * @return array|null
     */
    protected function getFirebaseData($notifiable): ?array
    {
        return property_exists($this, 'data') ? $this->data : null;
    }

    /**
     * Get the topic for Firebase.
     * Override this method to send to a topic instead of specific tokens.
     *
     * @param mixed $notifiable
     * @return string|null
     */
    protected function getFirebaseTopic($notifiable): ?string
    {
        return property_exists($this, 'topic') ? $this->topic : null;
    }

    /**
     * Get raw Firebase payload.
     * Override this method to use a completely custom payload.
     * When this returns a non-null value, all other fields are ignored.
     *
     * @param mixed $notifiable
     * @return array|null
     */
    protected function getFirebaseRaw($notifiable): ?array
    {
        return property_exists($this, 'raw') ? $this->raw : null;
    }

    /**
     * Get array-based Firebase payload.
     * Override this method to use array-based payload construction.
     * When this returns a non-null value, standard fields are ignored.
     *
     * @param mixed $notifiable
     * @return array|null
     */
    protected function getFirebaseArray($notifiable): ?array
    {
        return property_exists($this, 'fromArray') ? $this->fromArray : null;
    }

    /**
     * Get the delivery method (notification or message).
     * Override this method to change between notification and message delivery.
     * 
     * - 'notification': Sends as a notification (default)
     * - 'message': Sends as a data message
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function getFirebaseDeliveryMethod($notifiable): string
    {
        return property_exists($this, 'deliveryMethod') ? $this->deliveryMethod : 'notification';
    }

    /**
     * Get Android-specific configuration.
     * Override this method to provide Android platform customization.
     *
     * @param mixed $notifiable
     * @return \MeeeetDev\Larafirebase\Config\AndroidConfig|null
     */
    protected function getAndroidConfig($notifiable): ?\MeeeetDev\Larafirebase\Config\AndroidConfig
    {
        return property_exists($this, 'androidConfig') ? $this->androidConfig : null;
    }

    /**
     * Get APNS-specific configuration.
     * Override this method to provide iOS platform customization.
     *
     * @param mixed $notifiable
     * @return \MeeeetDev\Larafirebase\Config\ApnsConfig|null
     */
    protected function getApnsConfig($notifiable): ?\MeeeetDev\Larafirebase\Config\ApnsConfig
    {
        return property_exists($this, 'apnsConfig') ? $this->apnsConfig : null;
    }

    /**
     * Get Webpush-specific configuration.
     * Override this method to provide Web platform customization.
     *
     * @param mixed $notifiable
     * @return \MeeeetDev\Larafirebase\Config\WebpushConfig|null
     */
    protected function getWebpushConfig($notifiable): ?\MeeeetDev\Larafirebase\Config\WebpushConfig
    {
        return property_exists($this, 'webpushConfig') ? $this->webpushConfig : null;
    }

    /**
     * Get analytics label for tracking.
     * Override this method to add analytics tracking.
     *
     * @param mixed $notifiable
     * @return string|null
     */
    protected function getAnalyticsLabel($notifiable): ?string
    {
        return property_exists($this, 'analyticsLabel') ? $this->analyticsLabel : null;
    }

    /**
     * Get condition for conditional targeting.
     * Override this method to send to devices matching specific conditions.
     * Example: "'TopicA' in topics && 'TopicB' in topics"
     *
     * @param mixed $notifiable
     * @return string|null
     */
    protected function getCondition($notifiable): ?string
    {
        return property_exists($this, 'condition') ? $this->condition : null;
    }

    /**
     * Get the device tokens for Firebase.
     * Override this method to customize how tokens are retrieved.
     *
     * @param mixed $notifiable
     * @return string|array
     */
    protected function getFirebaseTokens($notifiable)
    {
        // Try to get tokens from the notifiable model
        if (method_exists($notifiable, 'getFcmTokens')) {
            return $notifiable->getFcmTokens();
        }

        if (method_exists($notifiable, 'getFcmToken')) {
            return $notifiable->getFcmToken();
        }

        if (property_exists($notifiable, 'fcm_token')) {
            return $notifiable->fcm_token;
        }

        if (property_exists($notifiable, 'fcm_tokens')) {
            return $notifiable->fcm_tokens;
        }

        // Fallback: check if tokens are set as a property on the notification
        if (property_exists($this, 'tokens')) {
            return $this->tokens;
        }

        throw new \Exception('Unable to retrieve FCM tokens. Please override getFirebaseTokens() method or ensure the notifiable has fcm_token/fcm_tokens property or getFcmToken()/getFcmTokens() method.');
    }
}
