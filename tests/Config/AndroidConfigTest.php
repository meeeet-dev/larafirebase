<?php

namespace MeeeetDev\Larafirebase\Tests\Config;

use PHPUnit\Framework\TestCase;
use MeeeetDev\Larafirebase\Config\AndroidConfig;
use MeeeetDev\Larafirebase\Config\AndroidNotification;

class AndroidConfigTest extends TestCase
{
    public function test_can_set_collapse_key()
    {
        $config = (new AndroidConfig)->withCollapseKey('test_key');
        $array = $config->toArray();
        
        $this->assertEquals('test_key', $array['collapse_key']);
    }

    public function test_can_set_priority()
    {
        $config = (new AndroidConfig)->withPriority('high');
        $array = $config->toArray();
        
        $this->assertEquals('high', $array['priority']);
    }

    public function test_priority_validation()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new AndroidConfig)->withPriority('invalid');
    }

    public function test_can_set_ttl()
    {
        $config = (new AndroidConfig)->withTtl(3600);
        $array = $config->toArray();
        
        $this->assertEquals('3600s', $array['ttl']);
    }

    public function test_ttl_validation_max()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new AndroidConfig)->withTtl(3000000); // More than 4 weeks
    }

    public function test_ttl_validation_min()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new AndroidConfig)->withTtl(-1);
    }

    public function test_can_set_restricted_package_name()
    {
        $config = (new AndroidConfig)->withRestrictedPackageName('com.example.app');
        $array = $config->toArray();
        
        $this->assertEquals('com.example.app', $array['restricted_package_name']);
    }

    public function test_can_set_data()
    {
        $data = ['key1' => 'value1', 'key2' => 123];
        $config = (new AndroidConfig)->withData($data);
        $array = $config->toArray();
        
        $this->assertEquals('value1', $array['data']['key1']);
        $this->assertEquals('123', $array['data']['key2']); // Should be converted to string
    }

    public function test_can_set_notification()
    {
        $notification = (new AndroidNotification)
            ->withTitle('Test')
            ->withBody('Body');
            
        $config = (new AndroidConfig)->withNotification($notification);
        $array = $config->toArray();
        
        $this->assertArrayHasKey('notification', $array);
        $this->assertEquals('Test', $array['notification']['title']);
    }

    public function test_can_set_direct_boot_ok()
    {
        $config = (new AndroidConfig)->withDirectBootOk(true);
        $array = $config->toArray();
        
        $this->assertTrue($array['direct_boot_ok']);
    }

    public function test_fluent_interface()
    {
        $config = (new AndroidConfig)
            ->withPriority('high')
            ->withTtl(3600)
            ->withCollapseKey('key');
            
        $this->assertInstanceOf(AndroidConfig::class, $config);
        
        $array = $config->toArray();
        $this->assertEquals('high', $array['priority']);
        $this->assertEquals('3600s', $array['ttl']);
        $this->assertEquals('key', $array['collapse_key']);
    }

    public function test_empty_config_returns_empty_array()
    {
        $config = new AndroidConfig;
        $array = $config->toArray();
        
        $this->assertEmpty($array);
    }
}
