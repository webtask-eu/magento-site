<?php

namespace LaminasTest\OAuth\TestAsset;

use Laminas\OAuth\Consumer;

class Consumer32874 extends Consumer
{
    public function getConsumerKey(): string
    {
        return '1234567890';
    }

    public function getSignatureMethod(): string
    {
        return 'HMAC-SHA1';
    }

    public function getVersion(): string
    {
        return '1.0';
    }

    public function getRequestTokenUrl(): string
    {
        return 'http://www.example.com/request';
    }

    public function getCallbackUrl(): string
    {
        return 'http://www.example.com/local';
    }
}
