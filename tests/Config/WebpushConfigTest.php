<?php

namespace MeeeetDev\Larafirebase\Tests\Config;

use PHPUnit\Framework\TestCase;
use MeeeetDev\Larafirebase\Config\WebpushConfig;

class WebpushConfigTest extends TestCase
{
    public function test_can_set_ttl()
    {
        $config = (new WebpushConfig)->withTtl(3600);
        $array = $config->toArray();
        
        $this->assertEquals('3600', $array['headers']['TTL']);
    }

    public function test_can_set_urgency()
    {
        $config = (new WebpushConfig)->withUrgency('high');
        $array = $config->toArray();
        
        $this->assertEquals('high', $array['headers']['Urgency']);
    }

    public function test_urgency_validation()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new WebpushConfig)->withUrgency('invalid');
    }

    public function test_can_set_topic()
    {
        $config = (new WebpushConfig)->withTopic('updates');
        $array = $config->toArray();
        
        $this->assertEquals('updates', $array['headers']['Topic']);
    }

    public function test_can_set_custom_headers()
    {
        $headers = ['Custom-Header' => 'value'];
        $config = (new WebpushConfig)->withHeaders($headers);
        $array = $config->toArray();
        
        $this->assertEquals('value', $array['headers']['Custom-Header']);
    }

    public function test_can_set_data()
    {
        $data = ['key1' => 'value1', 'key2' => 123];
        $config = (new WebpushConfig)->withData($data);
        $array = $config->toArray();
        
        $this->assertEquals('value1', $array['data']['key1']);
        $this->assertEquals('123', $array['data']['key2']); // Should be string
    }

    public function test_can_set_notification()
    {
        $notification = [
            'title' => 'Test',
            'body' => 'Body',
            'icon' => 'icon.png',
        ];
        
        $config = (new WebpushConfig)->withNotification($notification);
        $array = $config->toArray();
        
        $this->assertEquals($notification, $array['notification']);
    }

    public function test_can_set_link()
    {
        $config = (new WebpushConfig)->withLink('https://example.com/page');
        $array = $config->toArray();
        
        $this->assertEquals('https://example.com/page', $array['fcm_options']['link']);
    }

    public function test_link_validation_https()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new WebpushConfig)->withLink('http://example.com'); // Must be HTTPS
    }

    public function test_link_validation_url()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new WebpushConfig)->withLink('not-a-url');
    }

    public function test_can_set_analytics_label()
    {
        $config = (new WebpushConfig)->withAnalyticsLabel('campaign_1');
        $array = $config->toArray();
        
        $this->assertEquals('campaign_1', $array['fcm_options']['analytics_label']);
    }

    public function test_fluent_interface()
    {
        $config = (new WebpushConfig)
            ->withTtl(3600)
            ->withUrgency('high')
            ->withLink('https://example.com');
            
        $this->assertInstanceOf(WebpushConfig::class, $config);
    }

    public function test_empty_config_returns_empty_array()
    {
        $config = new WebpushConfig;
        $array = $config->toArray();
        
        $this->assertEmpty($array);
    }
}
