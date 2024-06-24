<?php

namespace MeeeetDev\Larafirebase\Services;

use Google\Client;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Google\Service\FirebaseCloudMessaging;
use MeeeetDev\Larafirebase\Exceptions\UnsupportedTokenFormat;

class Larafirebase
{
    const PRIORITY_NORMAL = 'normal';

    private $title;

    private $body;

    private $clickAction;

    private $image;

    private $icon;

    private $additionalData;

    private $sound;

    private $priority = self::PRIORITY_NORMAL;

    private $fromArray;

    private $topic;

    private $fromRaw;

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
    
    public function withClickAction($clickAction)
    {
        $this->clickAction = $clickAction;

        return $this;
    }

    public function withImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function withIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    public function withSound($sound)
    {
        $this->sound = $sound;

        return $this;
    }

    public function withPriority($priority)
    {
        $this->priority = $priority;

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

    public function sendNotification($tokens)
    {
        if($this->fromRaw) {
            return $this->callApi($this->fromRaw);
        }

        $payload = [
            'message' => [
                'registration_ids' => $this->validateToken($tokens),
                'notification' => [
                    'title' => $this->title,
                    'body' => $this->body,
                    'image' => $this->image,
                    'icon' => $this->icon,
                    'sound' => $this->sound,
                    'click_action' => $this->clickAction
                ],
                'data' => $this->additionalData,
                'priority' => $this->priority
            ],
        ];

        if($this->topic) {
            $payload['message']['topic'] = $this->topic;
        }

        return $this->callApi($payload);
    }

    public function sendMessage($tokens)
    {
        $data = ($this->fromArray) ? $this->fromArray : [
            'title' => $this->title,
            'body' => $this->body,
        ];

        $data = $this->additionalData ? array_merge($data, $this->additionalData) : $data;

        $fields = array(
            'registration_ids' => $this->validateToken($tokens),
            'data' => $data,
        );

        return $this->callApi($fields);
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
        $tenant = tenant();
        $cacheKey = 'LARAFIREBASE_AUTH_TOKEN';
        if (!is_null($tenant->firebase_config) && !empty($tenant->firebase_config)) {
            $firebaseCredentials = $tenant->firebase_config;
            $cacheKey .= '_' . $tenant->id;
        } else {
            $firebaseCredentials = config('larafirebase.firebase_credentials');
            $cacheKey .= '_CARDS';
        }

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
        $tenant = tenant();
        if (!is_null($tenant->firebase_project_id) && !empty($tenant->firebase_project_id)) {
            $firebaseProjectId = $tenant->firebase_project_id;
        } else {
            $firebaseProjectId = config('larafirebase.project_id');
        }

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
}
