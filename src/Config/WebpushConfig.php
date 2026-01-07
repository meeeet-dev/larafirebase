<?php

namespace MeeeetDev\Larafirebase\Config;

/**
 * Webpush protocol specific configuration
 * 
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#WebpushConfig
 */
class WebpushConfig
{
    private $headers = [];
    private $data;
    private $notification;
    private $link;
    private $analyticsLabel;

    /**
     * Set Webpush headers
     * 
     * @param array $headers Key-value pairs of webpush headers
     */
    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * Set TTL (Time-To-Live) header
     * 
     * @param int $seconds Number of seconds the message should be kept
     */
    public function withTtl(int $seconds): self
    {
        $this->headers['TTL'] = (string)$seconds;
        return $this;
    }

    /**
     * Set urgency header
     * 
     * @param string $urgency One of: very-low, low, normal, high
     */
    public function withUrgency(string $urgency): self
    {
        $validUrgencies = ['very-low', 'low', 'normal', 'high'];
        if (!in_array($urgency, $validUrgencies)) {
            throw new \InvalidArgumentException("Urgency must be one of: " . implode(', ', $validUrgencies));
        }
        $this->headers['Urgency'] = $urgency;
        return $this;
    }

    /**
     * Set topic for message replacement
     */
    public function withTopic(string $topic): self
    {
        $this->headers['Topic'] = $topic;
        return $this;
    }

    /**
     * Set web-specific data payload
     * Overrides the main message data field
     */
    public function withData(array $data): self
    {
        // Convert all values to strings as required by FCM
        $this->data = array_map('strval', $data);
        return $this;
    }

    /**
     * Set web notification options
     * Supports Web Notification API properties
     * 
     * @param array $notification Notification object (title, body, icon, badge, image, etc.)
     */
    public function withNotification(array $notification): self
    {
        $this->notification = $notification;
        return $this;
    }

    /**
     * Set the link to open when user clicks the notification
     * Must be HTTPS
     */
    public function withLink(string $url): self
    {
        if (!filter_var($url, FILTER_VALIDATE_URL) || !str_starts_with($url, 'https://')) {
            throw new \InvalidArgumentException("Link must be a valid HTTPS URL");
        }
        $this->link = $url;
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
     * Convert to array for FCM payload
     */
    public function toArray(): array
    {
        $config = [];

        if (!empty($this->headers)) {
            $config['headers'] = $this->headers;
        }

        if ($this->data !== null) {
            $config['data'] = $this->data;
        }

        if ($this->notification !== null) {
            $config['notification'] = $this->notification;
        }

        if ($this->link !== null || $this->analyticsLabel !== null) {
            $fcmOptions = [];
            if ($this->link !== null) {
                $fcmOptions['link'] = $this->link;
            }
            if ($this->analyticsLabel !== null) {
                $fcmOptions['analytics_label'] = $this->analyticsLabel;
            }
            $config['fcm_options'] = $fcmOptions;
        }

        return $config;
    }
}
