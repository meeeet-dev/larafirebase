<?php

namespace MeeeetDev\Larafirebase\Tests\Config;

use PHPUnit\Framework\TestCase;
use MeeeetDev\Larafirebase\Config\ApnsConfig;

class ApnsConfigTest extends TestCase
{
    public function test_can_set_priority()
    {
        $config = (new ApnsConfig)->withPriority(10);
        $array = $config->toArray();
        
        $this->assertEquals('10', $array['headers']['apns-priority']);
    }

    public function test_priority_validation()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ApnsConfig)->withPriority(7); // Only 5 or 10 allowed
    }

    public function test_can_set_expiration()
    {
        $timestamp = time() + 3600;
        $config = (new ApnsConfig)->withExpiration($timestamp);
        $array = $config->toArray();
        
        $this->assertEquals((string)$timestamp, $array['headers']['apns-expiration']);
    }

    public function test_can_set_collapse_id()
    {
        $config = (new ApnsConfig)->withCollapseId('collapse-1');
        $array = $config->toArray();
        
        $this->assertEquals('collapse-1', $array['headers']['apns-collapse-id']);
    }

    public function test_can_set_topic()
    {
        $config = (new ApnsConfig)->withTopic('com.example.app');
        $array = $config->toArray();
        
        $this->assertEquals('com.example.app', $array['headers']['apns-topic']);
    }

    public function test_can_set_custom_headers()
    {
        $headers = ['custom-header' => 'value'];
        $config = (new ApnsConfig)->withHeaders($headers);
        $array = $config->toArray();
        
        $this->assertEquals('value', $array['headers']['custom-header']);
    }

    public function test_can_set_payload()
    {
        $payload = [
            'aps' => [
                'alert' => 'Test',
                'badge' => 1,
            ],
        ];
        
        $config = (new ApnsConfig)->withPayload($payload);
        $array = $config->toArray();
        
        $this->assertEquals($payload, $array['payload']);
    }

    public function test_can_set_aps_payload()
    {
        $aps = ['alert' => 'Test', 'badge' => 1];
        $custom = ['custom_key' => 'value'];
        
        $config = (new ApnsConfig)->withApsPayload($aps, $custom);
        $array = $config->toArray();
        
        $this->assertEquals($aps, $array['payload']['aps']);
        $this->assertEquals('value', $array['payload']['custom_key']);
    }

    public function test_can_set_analytics_label()
    {
        $config = (new ApnsConfig)->withAnalyticsLabel('campaign_1');
        $array = $config->toArray();
        
        $this->assertEquals('campaign_1', $array['fcm_options']['analytics_label']);
    }

    public function test_can_set_image()
    {
        $config = (new ApnsConfig)->withImage('https://example.com/image.png');
        $array = $config->toArray();
        
        $this->assertEquals('https://example.com/image.png', $array['fcm_options']['image']);
    }

    public function test_can_set_live_activity_token()
    {
        $config = (new ApnsConfig)->withLiveActivityToken('token123');
        $array = $config->toArray();
        
        $this->assertEquals('token123', $array['live_activity_token']);
    }

    public function test_fluent_interface()
    {
        $config = (new ApnsConfig)
            ->withPriority(10)
            ->withTopic('com.example.app')
            ->withAnalyticsLabel('test');
            
        $this->assertInstanceOf(ApnsConfig::class, $config);
    }

    public function test_empty_config_returns_empty_array()
    {
        $config = new ApnsConfig;
        $array = $config->toArray();
        
        $this->assertEmpty($array);
    }
}
