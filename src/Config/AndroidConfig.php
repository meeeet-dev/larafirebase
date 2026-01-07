<?php

namespace MeeeetDev\Larafirebase\Config;

/**
 * Android-specific configuration for FCM messages
 * 
 * @see https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#AndroidConfig
 */
class AndroidConfig
{
    private $collapseKey;
    private $priority; // 'normal' or 'high'
    private $ttl; // Time to live in seconds
    private $restrictedPackageName;
    private $data;
    private $notification; // AndroidNotification instance
    private $directBootOk;

    /**
     * Set the collapse key for message grouping
     * Maximum of 4 different collapse keys allowed at any time
     */
    public function withCollapseKey(string $collapseKey): self
    {
        $this->collapseKey = $collapseKey;
        return $this;
    }

    /**
     * Set message priority
     * 
     * @param string $priority 'normal' or 'high'
     */
    public function withPriority(string $priority): self
    {
        if (!in_array($priority, ['normal', 'high'])) {
            throw new \InvalidArgumentException("Priority must be 'normal' or 'high'");
        }
        $this->priority = $priority;
        return $this;
    }

    /**
     * Set time-to-live in seconds
     * Maximum is 4 weeks (2419200 seconds)
     * Set to 0 to send immediately
     */
    public function withTtl(int $seconds): self
    {
        if ($seconds < 0 || $seconds > 2419200) {
            throw new \InvalidArgumentException("TTL must be between 0 and 2419200 seconds (4 weeks)");
        }
        $this->ttl = $seconds;
        return $this;
    }

    /**
     * Set the package name that must match to receive the message
     */
    public function withRestrictedPackageName(string $packageName): self
    {
        $this->restrictedPackageName = $packageName;
        return $this;
    }

    /**
     * Set Android-specific data payload
     * Overrides the main message data field
     */
    public function withData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set Android-specific notification options
     */
    public function withNotification(AndroidNotification $notification): self
    {
        $this->notification = $notification;
        return $this;
    }

    /**
     * Enable direct boot support
     * Allows notification to be received even when device is locked
     */
    public function withDirectBootOk(bool $ok): self
    {
        $this->directBootOk = $ok;
        return $this;
    }

    /**
     * Convert to array for FCM payload
     */
    public function toArray(): array
    {
        $config = [];

        if ($this->collapseKey !== null) {
            $config['collapse_key'] = $this->collapseKey;
        }

        if ($this->priority !== null) {
            $config['priority'] = $this->priority;
        }

        if ($this->ttl !== null) {
            $config['ttl'] = $this->ttl . 's'; // FCM expects duration format like "3.5s"
        }

        if ($this->restrictedPackageName !== null) {
            $config['restricted_package_name'] = $this->restrictedPackageName;
        }

        if ($this->data !== null) {
            // Convert all values to strings as required by FCM
            $config['data'] = array_map('strval', $this->data);
        }

        if ($this->notification !== null) {
            $config['notification'] = $this->notification->toArray();
        }

        if ($this->directBootOk !== null) {
            $config['direct_boot_ok'] = $this->directBootOk;
        }

        return $config;
    }
}
