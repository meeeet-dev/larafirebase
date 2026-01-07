<?php

namespace MeeeetDev\Larafirebase\Config;

/**
 * Android-specific notification options
 * 
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#AndroidNotification
 */
class AndroidNotification
{
    private $title;
    private $body;
    private $icon;
    private $color;
    private $sound;
    private $tag;
    private $clickAction;
    private $bodyLocKey;
    private $bodyLocArgs;
    private $titleLocKey;
    private $titleLocArgs;
    private $channelId;
    private $ticker;
    private $sticky;
    private $eventTime;
    private $localOnly;
    private $notificationPriority;
    private $defaultSound;
    private $defaultVibrateTimings;
    private $defaultLightSettings;
    private $vibrateTimings;
    private $visibility;
    private $notificationCount;
    private $lightSettings;
    private $image;

    public function withTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function withBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function withIcon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set notification color in #RRGGBB format
     */
    public function withColor(string $color): self
    {
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            throw new \InvalidArgumentException("Color must be in #RRGGBB format");
        }
        $this->color = $color;
        return $this;
    }

    public function withSound(string $sound): self
    {
        $this->sound = $sound;
        return $this;
    }

    public function withTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * Set the action associated with a user click on the notification
     */
    public function withClickAction(string $action): self
    {
        $this->clickAction = $action;
        return $this;
    }

    /**
     * Set the notification channel ID
     * Required for Android O and above
     */
    public function withChannelId(string $channelId): self
    {
        $this->channelId = $channelId;
        return $this;
    }

    public function withTicker(string $ticker): self
    {
        $this->ticker = $ticker;
        return $this;
    }

    /**
     * Set whether notification should be sticky
     * Sticky notifications are not dismissed when user swipes
     */
    public function withSticky(bool $sticky): self
    {
        $this->sticky = $sticky;
        return $this;
    }

    /**
     * Set event time in RFC3339 UTC format
     */
    public function withEventTime(string $timestamp): self
    {
        $this->eventTime = $timestamp;
        return $this;
    }

    /**
     * Set whether notification should be local only
     * Local-only notifications don't bridge to wearables
     */
    public function withLocalOnly(bool $localOnly): self
    {
        $this->localOnly = $localOnly;
        return $this;
    }

    /**
     * Set notification priority
     * 
     * @param string $priority One of: PRIORITY_MIN, PRIORITY_LOW, PRIORITY_DEFAULT, PRIORITY_HIGH, PRIORITY_MAX
     */
    public function withNotificationPriority(string $priority): self
    {
        $validPriorities = ['PRIORITY_MIN', 'PRIORITY_LOW', 'PRIORITY_DEFAULT', 'PRIORITY_HIGH', 'PRIORITY_MAX'];
        if (!in_array($priority, $validPriorities)) {
            throw new \InvalidArgumentException("Invalid notification priority. Must be one of: " . implode(', ', $validPriorities));
        }
        $this->notificationPriority = $priority;
        return $this;
    }

    /**
     * Set visibility
     * 
     * @param string $visibility One of: VISIBILITY_UNSPECIFIED, PRIVATE, PUBLIC, SECRET
     */
    public function withVisibility(string $visibility): self
    {
        $validVisibilities = ['VISIBILITY_UNSPECIFIED', 'PRIVATE', 'PUBLIC', 'SECRET'];
        if (!in_array($visibility, $validVisibilities)) {
            throw new \InvalidArgumentException("Invalid visibility. Must be one of: " . implode(', ', $validVisibilities));
        }
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * Set notification count (badge number)
     */
    public function withNotificationCount(int $count): self
    {
        $this->notificationCount = $count;
        return $this;
    }

    /**
     * Set LED light settings
     * 
     * @param string $color Color in #RRGGBB format
     * @param int $lightOnDurationMs Duration in milliseconds
     * @param int $lightOffDurationMs Duration in milliseconds
     */
    public function withLightSettings(string $color, int $lightOnDurationMs, int $lightOffDurationMs): self
    {
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            throw new \InvalidArgumentException("Color must be in #RRGGBB format");
        }

        $this->lightSettings = [
            'color' => [
                'red' => hexdec(substr($color, 1, 2)) / 255,
                'green' => hexdec(substr($color, 3, 2)) / 255,
                'blue' => hexdec(substr($color, 5, 2)) / 255,
            ],
            'light_on_duration' => ($lightOnDurationMs / 1000) . 's',
            'light_off_duration' => ($lightOffDurationMs / 1000) . 's',
        ];
        return $this;
    }

    /**
     * Set vibrate timings in milliseconds
     * 
     * @param array $timings Array of durations in milliseconds
     */
    public function withVibrateTimings(array $timings): self
    {
        $this->vibrateTimings = array_map(function ($ms) {
            return ($ms / 1000) . 's';
        }, $timings);
        return $this;
    }

    public function withDefaultSound(bool $default): self
    {
        $this->defaultSound = $default;
        return $this;
    }

    public function withDefaultVibrateTimings(bool $default): self
    {
        $this->defaultVibrateTimings = $default;
        return $this;
    }

    public function withDefaultLightSettings(bool $default): self
    {
        $this->defaultLightSettings = $default;
        return $this;
    }

    public function withImage(string $imageUrl): self
    {
        $this->image = $imageUrl;
        return $this;
    }

    /**
     * Convert to array for FCM payload
     */
    public function toArray(): array
    {
        $notification = [];

        if ($this->title !== null) $notification['title'] = $this->title;
        if ($this->body !== null) $notification['body'] = $this->body;
        if ($this->icon !== null) $notification['icon'] = $this->icon;
        if ($this->color !== null) $notification['color'] = $this->color;
        if ($this->sound !== null) $notification['sound'] = $this->sound;
        if ($this->tag !== null) $notification['tag'] = $this->tag;
        if ($this->clickAction !== null) $notification['click_action'] = $this->clickAction;
        if ($this->channelId !== null) $notification['channel_id'] = $this->channelId;
        if ($this->ticker !== null) $notification['ticker'] = $this->ticker;
        if ($this->sticky !== null) $notification['sticky'] = $this->sticky;
        if ($this->eventTime !== null) $notification['event_time'] = $this->eventTime;
        if ($this->localOnly !== null) $notification['local_only'] = $this->localOnly;
        if ($this->notificationPriority !== null) $notification['notification_priority'] = $this->notificationPriority;
        if ($this->defaultSound !== null) $notification['default_sound'] = $this->defaultSound;
        if ($this->defaultVibrateTimings !== null) $notification['default_vibrate_timings'] = $this->defaultVibrateTimings;
        if ($this->defaultLightSettings !== null) $notification['default_light_settings'] = $this->defaultLightSettings;
        if ($this->vibrateTimings !== null) $notification['vibrate_timings'] = $this->vibrateTimings;
        if ($this->visibility !== null) $notification['visibility'] = $this->visibility;
        if ($this->notificationCount !== null) $notification['notification_count'] = $this->notificationCount;
        if ($this->lightSettings !== null) $notification['light_settings'] = $this->lightSettings;
        if ($this->image !== null) $notification['image'] = $this->image;

        return $notification;
    }
}
