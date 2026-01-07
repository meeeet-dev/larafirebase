<?php

namespace MeeeetDev\Larafirebase\Tests\Config;

use PHPUnit\Framework\TestCase;
use MeeeetDev\Larafirebase\Config\AndroidNotification;

class AndroidNotificationTest extends TestCase
{
    public function test_can_set_title_and_body()
    {
        $notification = (new AndroidNotification)
            ->withTitle('Test Title')
            ->withBody('Test Body');
            
        $array = $notification->toArray();
        
        $this->assertEquals('Test Title', $array['title']);
        $this->assertEquals('Test Body', $array['body']);
    }

    public function test_can_set_color()
    {
        $notification = (new AndroidNotification)->withColor('#FF0000');
        $array = $notification->toArray();
        
        $this->assertEquals('#FF0000', $array['color']);
    }

    public function test_color_validation()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new AndroidNotification)->withColor('red'); // Invalid format
    }

    public function test_can_set_click_action()
    {
        $notification = (new AndroidNotification)->withClickAction('OPEN_ACTIVITY');
        $array = $notification->toArray();
        
        $this->assertEquals('OPEN_ACTIVITY', $array['click_action']);
    }

    public function test_can_set_channel_id()
    {
        $notification = (new AndroidNotification)->withChannelId('channel_1');
        $array = $notification->toArray();
        
        $this->assertEquals('channel_1', $array['channel_id']);
    }

    public function test_can_set_sound()
    {
        $notification = (new AndroidNotification)->withSound('custom_sound');
        $array = $notification->toArray();
        
        $this->assertEquals('custom_sound', $array['sound']);
    }

    public function test_can_set_notification_priority()
    {
        $notification = (new AndroidNotification)->withNotificationPriority('PRIORITY_HIGH');
        $array = $notification->toArray();
        
        $this->assertEquals('PRIORITY_HIGH', $array['notification_priority']);
    }

    public function test_notification_priority_validation()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new AndroidNotification)->withNotificationPriority('INVALID');
    }

    public function test_can_set_visibility()
    {
        $notification = (new AndroidNotification)->withVisibility('PUBLIC');
        $array = $notification->toArray();
        
        $this->assertEquals('PUBLIC', $array['visibility']);
    }

    public function test_visibility_validation()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new AndroidNotification)->withVisibility('INVALID');
    }

    public function test_can_set_light_settings()
    {
        $notification = (new AndroidNotification)->withLightSettings('#00FF00', 1000, 500);
        $array = $notification->toArray();
        
        $this->assertArrayHasKey('light_settings', $array);
        $this->assertArrayHasKey('color', $array['light_settings']);
        $this->assertEquals('1s', $array['light_settings']['light_on_duration']);
        $this->assertEquals('0.5s', $array['light_settings']['light_off_duration']);
    }

    public function test_can_set_vibrate_timings()
    {
        $notification = (new AndroidNotification)->withVibrateTimings([100, 200, 300]);
        $array = $notification->toArray();
        
        $this->assertEquals(['0.1s', '0.2s', '0.3s'], $array['vibrate_timings']);
    }

    public function test_can_set_notification_count()
    {
        $notification = (new AndroidNotification)->withNotificationCount(5);
        $array = $notification->toArray();
        
        $this->assertEquals(5, $array['notification_count']);
    }

    public function test_can_set_sticky()
    {
        $notification = (new AndroidNotification)->withSticky(true);
        $array = $notification->toArray();
        
        $this->assertTrue($array['sticky']);
    }

    public function test_fluent_interface()
    {
        $notification = (new AndroidNotification)
            ->withTitle('Title')
            ->withBody('Body')
            ->withColor('#FF0000')
            ->withSound('default');
            
        $this->assertInstanceOf(AndroidNotification::class, $notification);
        
        $array = $notification->toArray();
        $this->assertCount(4, $array);
    }
}
