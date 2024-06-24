<?php

namespace MeeeetDev\Larafirebase\Messages;

use MeeeetDev\Larafirebase\Facades\Larafirebase;

class FirebaseMessage extends Larafirebase
{
    public function withTitle($title)
    {
        return parent::withTitle($title);
    }

    public function withBody($body)
    {
        return parent::withBody($body);
    }

    public function withImage($image)
    {
        return parent::withImage($image);
    }

    public function withAdditionalData($additionalData)
    {
        return parent::withAdditionalData($additionalData);
    }

    public function withTopic($topic)
    {
        return parent::withTopic($topic);
    }

    public function fromArray($fromArray)
    {
        return parent::fromArray($fromArray);
    }

    public function fromRaw($fromRaw)
    {
        return parent::fromRaw($fromRaw);
    }

    public function asNotification($tokens)
    {
        return parent::sendNotification($tokens);
    }
}