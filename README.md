<p align="center">
    <a href="https://packagist.org/packages/meeeet-dev/larafirebase">
        <img src="https://img.shields.io/packagist/dt/meeeet-dev/larafirebase" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/meeeet-dev/larafirebase">
        <img src="https://img.shields.io/packagist/v/meeeet-dev/larafirebase" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/meeeet-dev/larafirebase">
        <img src="https://img.shields.io/packagist/l/meeeet-dev/larafirebase" alt="License">
    </a>
</p>


### Introduction

**Larafirebase** is a package thats offers you to send push notifications via Firebase in Laravel.

Firebase Cloud Messaging (FCM) is a cross-platform messaging solution that lets you reliably deliver messages at no cost.

For use cases such as instant messaging, a message can transfer a payload of up to 4KB to a client app.

### Installation

Follow the steps below to install the package.


**Install via Composer**

```
composer require meeeet-dev/larafirebase
```

**Copy Configuration**

Run the following command to publish the `larafirebase.php` config file:

```bash
php artisan vendor:publish --provider="MeeeetDev\Larafirebase\Providers\LarafirebaseServiceProvider"
```

**Configure larafirebase.php as needed**

Open the `larafirebase.php` configuration file, which you just published, and set the following values as needed:

- `project_id`: Replace with your actual Firebase project ID.
- `firebase_credentials`: This refers to the JSON credentials file for your Firebase project. Make sure it points to the correct location in your project. This JSON file contains the authentication information for your Firebase project, allowing your Laravel application to interact with Firebase services. You can generate this JSON file in the Firebase Console. Once you have it, specify its path in this configuration.

### Usage

Follow the steps below to find how to use the package.

Example usage in any class you want to use Larafirebase:

```php
use MeeeetDev\Larafirebase\Facades\Larafirebase;

class MyController
{
    private $deviceTokens =['{TOKEN_1}', '{TOKEN_2}'];

    public function sendNotification()
    {
        return Larafirebase::withTitle('Hello World')
            ->withBody('I have something new to share with you!')
            ->withImage('https://firebase.google.com/images/social.png')
            ->withAdditionalData([
                'name' => 'wrench',
                'mass' => '1.3kg',
                'count' => '3'
            ])
            ->sendNotification($this->deviceTokens);
        
        // Or
        return Larafirebase::fromRaw([
            // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages
            "name" => "string",
            "data" => [
                "string" => "string",
            ],
            "notification" => [
                "object" => "(Notification)"
            ],
            "android" => [
                "object" => "(AndroidConfig)"
            ],
            "webpush" => [
                "object" => "(WebpushConfig)",
            ],
            "apns" => [
                "object" => "(ApnsConfig)"
            ],
            "fcm_options" => [
                "object" => "(FcmOptions)"
            ],
            "token" => "string",
            "topic" => "string",
            "condition" => "string"
        ])->sendNotification($this->deviceTokens);
    }
}
```

### Using the HasFirebaseNotification Trait

The package provides a reusable `HasFirebaseNotification` trait that simplifies implementing Firebase notifications. This is the **recommended approach** for most use cases.

#### Available Customization Fields

The trait supports all Firebase message fields. You can customize by setting properties or overriding methods:

| Field | Property | Method Override | Description |
|-------|----------|----------------|-------------|
| **Title** | `$title` | `getFirebaseTitle()` | Notification title |
| **Body** | `$body` | `getFirebaseBody()` | Notification body text |
| **Image** | `$image` | `getFirebaseImage()` | Image URL for rich notifications |
| **Data** | `$data` | `getFirebaseData()` | Additional custom data payload |
| **Topic** | `$topic` | `getFirebaseTopic()` | Topic name for topic-based messaging |
| **Tokens** | `$tokens` | `getFirebaseTokens()` | Device tokens (auto-detected from user) |
| **Delivery Method** | `$deliveryMethod` | `getFirebaseDeliveryMethod()` | `'notification'` or `'message'` |
| **Raw Payload** | `$raw` | `getFirebaseRaw()` | Complete custom payload (overrides all) |
| **Array Payload** | `$fromArray` | `getFirebaseArray()` | Array-based payload construction |

---

#### Option 1: Basic Usage (Simple & Quick)

Perfect for straightforward notifications with static or simple dynamic content.

```php
use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Traits\HasFirebaseNotification;

class OrderConfirmed extends Notification
{
    use HasFirebaseNotification;

    public $title = 'Order Confirmed';
    public $body;
    public $image;
    public $data;

    public function __construct($orderNumber)
    {
        $this->body = "Your order #{$orderNumber} has been confirmed!";
        $this->data = ['order_number' => $orderNumber];
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }
}
```

**Usage:**
```php
$user->notify(new OrderConfirmed('12345'));
```

---

#### Option 2: Advanced Usage (Custom Logic)

Override trait methods for complete control over notification content and behavior.

```php
use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Traits\HasFirebaseNotification;

class WelcomeNotification extends Notification
{
    use HasFirebaseNotification;

    private $user;
    private $referralCode;

    public function __construct($user, $referralCode = null)
    {
        $this->user = $user;
        $this->referralCode = $referralCode;
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }

    protected function getFirebaseTitle($notifiable): string
    {
        return "Welcome, {$this->user->name}! 🎉";
    }

    protected function getFirebaseBody($notifiable): string
    {
        return $this->referralCode 
            ? "Thanks for joining via referral code: {$this->referralCode}"
            : "We're excited to have you on board!";
    }

    protected function getFirebaseImage($notifiable): ?string
    {
        return 'https://example.com/welcome-banner.png';
    }

    protected function getFirebaseData($notifiable): ?array
    {
        return [
            'user_id' => $this->user->id,
            'referral_code' => $this->referralCode,
            'action' => 'open_profile',
            'timestamp' => now()->toIso8601String()
        ];
    }

    protected function getFirebaseTokens($notifiable)
    {
        // Get all active device tokens
        return $notifiable->deviceTokens()
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();
    }
}
```

**Usage:**
```php
$user->notify(new WelcomeNotification($user, 'REF123'));
```

---

#### Option 3: Manual Implementation (Without Trait)

Implement the `toFirebase` method directly if you prefer full manual control.

```php
use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Messages\FirebaseMessage;

class SendBirthdayReminder extends Notification
{
    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['firebase'];
    }

    /**
     * Get the firebase representation of the notification.
     */
    public function toFirebase($notifiable)
    {
        $deviceTokens = [
            '{TOKEN_1}',
            '{TOKEN_2}'
        ];

        return (new FirebaseMessage)
            ->withTitle('Hey, ', $notifiable->first_name)
            ->withBody('Happy Birthday!')
            ->asNotification($deviceTokens); // OR ->asMessage($deviceTokens);
    }
}
```

---

## Platform-Specific Configurations

The package supports full FCM v1 API features with platform-specific configurations for Android, iOS/APNS, and Web push notifications.

### Android Configuration

Customize notifications for Android devices with priority, TTL, colors, sounds, and more.

```php
use MeeeetDev\Larafirebase\Config\AndroidConfig;
use MeeeetDev\Larafirebase\Config\AndroidNotification;

class OrderNotification extends Notification
{
    use HasFirebaseNotification;

    protected function getAndroidConfig($notifiable): ?AndroidConfig
    {
        $androidNotification = (new AndroidNotification)
            ->withTitle('Order Confirmed')
            ->withBody('Your order is on the way!')
            ->withColor('#4CAF50')              // Notification color
            ->withSound('order_sound')          // Custom sound
            ->withChannelId('orders')           // Notification channel
            ->withClickAction('OPEN_ORDER')     // Click action
            ->withNotificationPriority('PRIORITY_HIGH')
            ->withLightSettings('#4CAF50', 1000, 500); // LED color & timing

        return (new AndroidConfig)
            ->withPriority('high')              // Message priority
            ->withTtl(86400)                    // 24 hours TTL
            ->withCollapseKey('order_updates')  // Group related messages
            ->withNotification($androidNotification);
    }
}
```

**Available Android Options:**

| Method | Description |
|--------|-------------|
| `withPriority('high')` | Message priority: 'normal' or 'high' |
| `withTtl(seconds)` | Time-to-live (0-2419200 seconds) |
| `withCollapseKey(key)` | Group messages (max 4 keys) |
| `withRestrictedPackageName(name)` | Restrict to specific app |
| `withDirectBootOk(bool)` | Deliver when device locked |

**Android Notification Options:**

| Method | Description |
|--------|-------------|
| `withColor('#RRGGBB')` | Notification color |
| `withSound(sound)` | Custom sound file |
| `withChannelId(id)` | Notification channel (required for Android O+) |
| `withClickAction(action)` | Action on click |
| `withNotificationPriority(priority)` | PRIORITY_MIN, LOW, DEFAULT, HIGH, MAX |
| `withVisibility(visibility)` | PRIVATE, PUBLIC, SECRET |
| `withLightSettings(color, onMs, offMs)` | LED light settings |
| `withVibrateTimings([ms...])` | Vibration pattern |
| `withNotificationCount(count)` | Badge number |
| `withSticky(bool)` | Prevent swipe dismiss |

---

### iOS/APNS Configuration

Customize notifications for iOS devices with APNS-specific options.

```php
use MeeeetDev\Larafirebase\Config\ApnsConfig;

class OrderNotification extends Notification
{
    use HasFirebaseNotification;

    protected function getApnsConfig($notifiable): ?ApnsConfig
    {
        $apsPayload = [
            'alert' => [
                'title' => 'Order Confirmed',
                'body' => 'Your order is on the way!',
            ],
            'badge' => 1,
            'sound' => 'default',
            'category' => 'ORDER_CATEGORY',
        ];

        return (new ApnsConfig)
            ->withPriority(10)                  // 5=normal, 10=high
            ->withApsPayload($apsPayload)
            ->withAnalyticsLabel('order_campaign')
            ->withImage('https://example.com/image.png');
    }
}
```

**Available APNS Options:**

| Method | Description |
|--------|-------------|
| `withPriority(priority)` | 5 (normal) or 10 (high) |
| `withExpiration(timestamp)` | Unix timestamp when message expires |
| `withCollapseId(id)` | Replace previous notifications |
| `withTopic(topic)` | App bundle ID |
| `withPayload(array)` | Complete APNS payload |
| `withApsPayload(aps, custom)` | APS dictionary + custom data |
| `withAnalyticsLabel(label)` | Analytics tracking label |
| `withImage(url)` | Image URL override |
| `withLiveActivityToken(token)` | iOS Live Activity token |

---

### Web Push Configuration

Customize notifications for web browsers.

```php
use MeeeetDev\Larafirebase\Config\WebpushConfig;

class OrderNotification extends Notification
{
    use HasFirebaseNotification;

    protected function getWebpushConfig($notifiable): ?WebpushConfig
    {
        $webNotification = [
            'title' => 'Order Confirmed',
            'body' => 'Your order is on the way!',
            'icon' => 'https://example.com/icon.png',
            'badge' => 'https://example.com/badge.png',
            'image' => 'https://example.com/order.png',
        ];

        return (new WebpushConfig)
            ->withNotification($webNotification)
            ->withLink('https://example.com/orders/123')
            ->withUrgency('high')
            ->withTtl(86400);
    }
}
```

**Available Webpush Options:**

| Method | Description |
|--------|-------------|
| `withTtl(seconds)` | Time-to-live in seconds |
| `withUrgency(urgency)` | very-low, low, normal, high |
| `withTopic(topic)` | Message replacement topic |
| `withNotification(array)` | Web Notification API object |
| `withLink(url)` | Click action URL (must be HTTPS) |
| `withAnalyticsLabel(label)` | Analytics tracking label |

---

### Complete Platform-Specific Example

```php
use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Traits\HasFirebaseNotification;
use MeeeetDev\Larafirebase\Config\{AndroidConfig, AndroidNotification, ApnsConfig, WebpushConfig};

class MultiPlatformNotification extends Notification
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

    // Android customization
    protected function getAndroidConfig($notifiable): ?AndroidConfig
    {
        $notification = (new AndroidNotification)
            ->withColor('#4CAF50')
            ->withSound('order_sound')
            ->withChannelId('orders');

        return (new AndroidConfig)
            ->withPriority('high')
            ->withNotification($notification);
    }

    // iOS customization
    protected function getApnsConfig($notifiable): ?ApnsConfig
    {
        return (new ApnsConfig)
            ->withPriority(10)
            ->withApsPayload([
                'alert' => ['title' => 'Order Update', 'body' => 'Confirmed!'],
                'badge' => 1,
            ]);
    }

    // Web customization
    protected function getWebpushConfig($notifiable): ?WebpushConfig
    {
        return (new WebpushConfig)
            ->withLink("https://example.com/orders/{$this->order['id']}")
            ->withUrgency('high');
    }

    // Common fields
    protected function getFirebaseTitle($notifiable): string
    {
        return 'Order Confirmed';
    }

    protected function getFirebaseBody($notifiable): string
    {
        return "Order #{$this->order['id']} confirmed!";
    }

    protected function getAnalyticsLabel($notifiable): ?string
    {
        return 'order_confirmation_campaign';
    }
}
```

---

### Condition-Based Targeting

Send to devices matching specific conditions:

```php
class ConditionalNotification extends Notification
{
    use HasFirebaseNotification;

    protected function getCondition($notifiable): ?string
    {
        // Send to users subscribed to both topics
        return "'news' in topics && 'breaking' in topics";
    }

    // ... other methods
}
```

---

### Troubleshooting

#### ❌ Error: "The notification is missing a toFirebase method"

**Problem:** Your notification class doesn't have a `toFirebase()` method.

**Solution 1 - Use the Trait (Recommended):**

```php
use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Traits\HasFirebaseNotification;

class YourNotification extends Notification
{
    use HasFirebaseNotification;

    public $title = 'Hello';
    public $body = 'Your message here';

    public function via($notifiable)
    {
        return ['firebase'];
    }
}
```

**Solution 2 - Implement Manually:**

```php
use Illuminate\Notifications\Notification;
use MeeeetDev\Larafirebase\Messages\FirebaseMessage;

class YourNotification extends Notification
{
    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function toFirebase($notifiable)
    {
        $tokens = $notifiable->fcm_token;

        return (new FirebaseMessage)
            ->withTitle('Notification Title')
            ->withBody('Notification body text')
            ->asNotification($tokens);
    }
}
```

### Tips

- You can use `larafirebase()` helper instead of Facade.

### Payload

Check how is formed payload to send to firebase:

Example 1:

```php
Larafirebase::withTitle('Test Title')->withBody('Test body')->sendNotification('token1');
```

```json
{
  "token": "token1",
  "message" : {
        "notification": {
        "title": "Test Title",
        "body": "Test body"
    }
  },
}
```

Example 2:

```php
Larafirebase::withTitle('Test Title')->withBody('Test body')->sendMessage('token1');
```

```json
{
  "token": "token1",
  "message" : {
      "data": {
        "title": "Test Title",
        "body": "Test body"
      }
  }
}
```

If you want to create payload from scratch you can use method `fromRaw`, for example:

```php
return Larafirebase::fromRaw([
    'token' => 'token1',
    'data' => [
        'key_1' => 'Value 1',
        'key_2' => 'Value 2'
    ],
    'android' => [
        'ttl' => '1000s',
        'priority' => 'normal',
        'notification' => [
            'key_1' => 'Value 1',
            'key_2' => 'Value 2'
        ],
    ],
])->send();
```

---


<sup>Made with ♥ by Meet Bhanabhagwan ([@meeeet-dev](https://github.com/meeeet-dev)).</sup>