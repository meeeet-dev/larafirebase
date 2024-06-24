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
            ->withIcon('https://seeklogo.com/images/F/firebase-logo-402F407EE0-seeklogo.com.png')
            ->withSound('default')
            ->withClickAction('https://www.google.com')
            ->withPriority('high')
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

Example usage in **Notification** class:

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
  "registration_ids": [
    "token1"
  ],
  "notification": {
    "title": "Test Title",
    "body": "Test body"
  },
  "priority": "normal"
}
```

Example 2:

```php
Larafirebase::withTitle('Test Title')->withBody('Test body')->sendMessage('token1');
```

```json
{
  "registration_ids": [
    "token1"
  ],
  "data": {
    "title": "Test Title",
    "body": "Test body"
  }
}
```

If you want to create payload from scratch you can use method `fromRaw`, for example:

```php
return Larafirebase::fromRaw([
    'registration_ids' => ['token1', 'token2'],
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


<sup>Made with ♥ by Meeeet Dev ([@meeeet-dev](https://github.com/meeeet-dev)).</sup>