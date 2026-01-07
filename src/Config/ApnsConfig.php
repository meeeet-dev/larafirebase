<?php

namespace MeeeetDev\Larafirebase\Config;

/**
 * Apple Push Notification Service (APNS) specific configuration
 * 
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#ApnsConfig
 */
class ApnsConfig
{
    private $headers = [];
    private $payload;
    private $analyticsLabel;
    private $image;
    private $liveActivityToken;

    /**
     * Set APNS headers
     * 
     * @param array $headers Key-value pairs of APNS headers
     */
    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * Set APNS priority
     * 
     * @param int $priority 5 for normal, 10 for high
     */
    public function withPriority(int $priority): self
    {
        if (!in_array($priority, [5, 10])) {
            throw new \InvalidArgumentException("APNS priority must be 5 (normal) or 10 (high)");
        }
        $this->headers['apns-priority'] = (string)$priority;
        return $this;
    }

    /**
     * Set APNS expiration timestamp
     * 
     * @param int $timestamp Unix timestamp when message expires
     */
    public function withExpiration(int $timestamp): self
    {
        $this->headers['apns-expiration'] = (string)$timestamp;
        return $this;
    }

    /**
     * Set APNS collapse ID
     * Multiple notifications with same collapse ID will replace each other
     */
    public function withCollapseId(string $collapseId): self
    {
        $this->headers['apns-collapse-id'] = $collapseId;
        return $this;
    }

    /**
     * Set APNS topic (usually the app bundle ID)
     */
    public function withTopic(string $topic): self
    {
        $this->headers['apns-topic'] = $topic;
        return $this;
    }

    /**
     * Set the complete APNS payload
     * This should include the 'aps' dictionary and any custom data
     * 
     * @param array $payload Complete APNS payload
     */
    public function withPayload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Set analytics label for tracking
     */
    public function withAnalyticsLabel(string $label): self
    {
        $this->analyticsLabel = $label;
        return $this;
    }

    /**
     * Set image URL (overrides the main notification image)
     */
    public function withImage(string $imageUrl): self
    {
        $this->image = $imageUrl;
        return $this;
    }

    /**
     * Set Live Activity token for iOS Live Activities
     * Can be either a push token or push-to-start token
     */
    public function withLiveActivityToken(string $token): self
    {
        $this->liveActivityToken = $token;
        return $this;
    }

    /**
     * Helper method to set common APS payload options
     * 
     * @param array $aps APS dictionary (alert, badge, sound, etc.)
     * @param array $customData Optional custom data fields
     */
    public function withApsPayload(array $aps, array $customData = []): self
    {
        $this->payload = array_merge(['aps' => $aps], $customData);
        return $this;
    }

    /**
     * Convert to array for FCM payload
     */
    public function toArray(): array
    {
        $config = [];

        if (!empty($this->headers)) {
            $config['headers'] = $this->headers;
        }

        if ($this->payload !== null) {
            $config['payload'] = $this->payload;
        }

        if ($this->analyticsLabel !== null || $this->image !== null) {
            $fcmOptions = [];
            if ($this->analyticsLabel !== null) {
                $fcmOptions['analytics_label'] = $this->analyticsLabel;
            }
            if ($this->image !== null) {
                $fcmOptions['image'] = $this->image;
            }
            $config['fcm_options'] = $fcmOptions;
        }

        if ($this->liveActivityToken !== null) {
            $config['live_activity_token'] = $this->liveActivityToken;
        }

        return $config;
    }
}
