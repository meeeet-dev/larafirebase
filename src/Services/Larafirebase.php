<?php

namespace MeeeetDev\Larafirebase\Services;

use Google\Client;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Google\Service\FirebaseCloudMessaging;
use MeeeetDev\Larafirebase\Exceptions\BadRequestFormat;
use MeeeetDev\Larafirebase\Exceptions\UnsupportedTokenFormat;
use MeeeetDev\Larafirebase\Config\AndroidConfig;
use MeeeetDev\Larafirebase\Config\ApnsConfig;
use MeeeetDev\Larafirebase\Config\WebpushConfig;

class Larafirebase
{
    private $title;

    private $body;

    private $image;

    private $additionalData;

    private $fromArray;

    private $topic;

    private $fromRaw;

    private $androidConfig;

    private $apnsConfig;

    private $webpushConfig;

    private $analyticsLabel;

    private $condition;

    public const API_URI = 'https://fcm.googleapis.com/v1/projects/:projectId/messages:send';

    public function withTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function withBody($body)
    {
        $this->body = $body;

        return $this;
    }

    public function withImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function withAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;

        return $this;
    }

    public function withTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }

    public function fromArray($fromArray)
    {
        $this->fromArray = $fromArray;

        return $this;
    }

    public function fromRaw($fromRaw)
    {
        $this->fromRaw = $fromRaw;

        return $this;
    }

    public function withAndroidConfig(AndroidConfig $config)
    {
        $this->androidConfig = $config;

        return $this;
    }

    public function withApnsConfig(ApnsConfig $config)
    {
        $this->apnsConfig = $config;

        return $this;
    }

    public function withWebpushConfig(WebpushConfig $config)
    {
        $this->webpushConfig = $config;

        return $this;
    }

    public function withAnalyticsLabel($label)
    {
        $this->analyticsLabel = $label;

        return $this;
    }

    public function withCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    public function sendNotification($tokens)
    {
        if($this->fromRaw) {
            return $this->callApi($this->fromRaw);
        }

        $devicetokens = $this->validateToken($tokens);

        if (count($devicetokens) == 0) {
            return true;
        }

        $additionalData = $this->additionalData;
        if (is_array($additionalData)) {
            $data = collect($additionalData)->map(function ($item, $key) {
                // Convert each item to string
                if (is_array($item)) {
                    return json_encode($item); // Convert all array values to strings
                } else {
                    return strval($item); // Convert scalar value to string
                }
            })->toArray();
        } else {
            $data = $additionalData;
        }

        if ($this->topic) {
            $payload = [
                'message' => [
                    'topic' => $this->topic,
                    'notification' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'data' => $data,
                ],
            ];

            if ($this->image) {
                $payload['message']['notification']['image'] = $this->image;
            }

            // Add platform-specific configurations
            $this->addPlatformConfigs($payload['message']);

            $res = $this->callApi($payload);

            if ($res->getStatusCode() == 404) {
                // Requested entity was not found, Ignore error
            } else if ($res->getStatusCode() != 200) {
                throw new BadRequestFormat('Failed to send notification. status code: ' . $res->getStatusCode());
            }

            return true;
        }

        // Handle condition-based targeting
        if ($this->condition) {
            $payload = [
                'message' => [
                    'condition' => $this->condition,
                    'notification' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'data' => $data,
                ],
            ];

            if ($this->image) {
                $payload['message']['notification']['image'] = $this->image;
            }

            // Add platform-specific configurations
            $this->addPlatformConfigs($payload['message']);

            $res = $this->callApi($payload);

            if ($res->getStatusCode() == 404) {
                // Requested entity was not found, Ignore error
            } else if ($res->getStatusCode() != 200) {
                throw new BadRequestFormat('Failed to send notification. status code: ' . $res->getStatusCode());
            }

            return true;
        }

        foreach ($devicetokens as $key => $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'data' => $data,
                ],
            ];

            if ($this->image) {
                $payload['message']['notification']['image'] = $this->image;
            }

            if($this->topic) {
                $payload['message']['topic'] = $this->topic;
            }

            // Add platform-specific configurations
            $this->addPlatformConfigs($payload['message']);

            $res = $this->callApi($payload);

            if ($res->getStatusCode() == 404) {
                // Requested entity was not found, Ignore error
            } else if ($res->getStatusCode() != 200) {
                throw new BadRequestFormat('Failed to send notification. status code: ' . $res->getStatusCode());
            }
        }

        return true;
    }

    /**
     * Add platform-specific configurations to the message payload
     */
    private function addPlatformConfigs(array &$message)
    {
        if ($this->androidConfig !== null) {
            $message['android'] = $this->androidConfig->toArray();
        }

        if ($this->apnsConfig !== null) {
            $message['apns'] = $this->apnsConfig->toArray();
        }

        if ($this->webpushConfig !== null) {
            $message['webpush'] = $this->webpushConfig->toArray();
        }

        if ($this->analyticsLabel !== null) {
            $message['fcm_options'] = ['analytics_label' => $this->analyticsLabel];
        }
    }

    public function sendMessage($tokens)
    {
        return $this->sendNotification($tokens);
    }

    public function send()
    {
        return $this->callApi($this->fromRaw);
    }

    /**
     * @return string
     * @throws \Google\Exception
     */
    private function getBearerToken(): string
    {
        $cacheKey = 'LARAFIREBASE_AUTH_TOKEN';
        $firebaseCredentials = config('larafirebase.firebase_credentials');
        $cacheKey .= '_CARDS';

        $client = new Client();
        $client->setAuthConfig($firebaseCredentials);
        $client->addScope(FirebaseCloudMessaging::CLOUD_PLATFORM);

        /* TODO
         * Date: 24/06/2024
         * Temporary fix for Cache not supporting tags
         */
        // $savedToken = Cache::get($cacheKey);
        $savedToken = false;

        if (!$savedToken) {
            $accessToken = $this->generateNewBearerToken($client, $cacheKey);
            $client->setAccessToken($accessToken);

            return $accessToken['access_token'];
        }

        $client->setAccessToken($savedToken);

        if (!$client->isAccessTokenExpired()) {
            return json_decode($savedToken)->access_token;
        }

        $newAccessToken = $this->generateNewBearerToken($client, $cacheKey);
        $client->setAccessToken($newAccessToken);
        return $newAccessToken['access_token'];
    }

    /**
     * @param $client
     * @param $cacheKey
     * @return array
     */
    private function generateNewBearerToken($client, $cacheKey): array
    {
        $client->fetchAccessTokenWithAssertion();
        $accessToken = $client->getAccessToken();

        //$tokenJson = json_encode($accessToken);
        //Cache::add($cacheKey, $tokenJson);

        return $accessToken;
    }

    /**
     * @param $fields
     * @return Response
     * @throws \Google\Exception
     */
    private function callApi($fields): Response
    {
        $firebaseProjectId = config('larafirebase.project_id');

        $apiURL = str_replace(':projectId', $firebaseProjectId, self::API_URI);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getBearerToken()
        ])->post($apiURL, $fields);

        return $response;
    }

    private function validateToken($tokens)
    {
        if (is_array($tokens)) {
            return $tokens;
        }

        if (is_string($tokens)) {
            return explode(',', $tokens);
        }

        throw new UnsupportedTokenFormat('Please pass tokens as array [token1, token2] or as string (use comma as separator if multiple passed).');
    }

    public function asNotification($tokens)
    {
        return $this->sendNotification($tokens);
    }

    public function asMessage($tokens)
    {
        return $this->sendMessage($tokens);
    }
}
