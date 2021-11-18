<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit;

use GitBalocco\LaravelNotificationTemplate\Common\LaravelCommonConfig;
use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use GitBalocco\LaravelNotificationTemplate\Entity\DtoObjects;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;
use GitBalocco\LaravelNotificationTemplate\Service\NotificationSettingService;
use GitBalocco\LaravelNotificationTemplate\TemplatedNotification;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\TemplatedNotification
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\TemplatedNotificationTest
 */
class TemplatedNotificationTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = TemplatedNotification::class;

    /**
     * @covers ::via
     */
    public function test_via()
    {
        $notifiable = '';

        $mock = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getSetting->via')->andReturn(
            [
                new NotificationChannel('mail'),
                new NotificationChannel('database')
            ]
        );
        $actual = $mock->via($notifiable);
        $this->assertSame(['mail', 'database'], $actual);
    }

    /**
     * @covers ::toMail
     */
    public function test_toMail()
    {
        $notifiable = new \stdClass();
        $channel = new NotificationChannel('mail');

        App::shouldReceive('make')
            ->with(NotificationChannel::class, ['value' => 'mail'])
            ->once()
            ->andReturn($channel);

        $mock = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('buildByDriver')
            ->with($channel, $notifiable)
            ->once();

        $mock->toMail($notifiable);
    }

    /**
     * @covers ::toDatabase
     */
    public function test_toDatabase()
    {
        $notifiable = new \stdClass();
        $channel = new NotificationChannel('database');

        App::shouldReceive('make')
            ->with(NotificationChannel::class, ['value' => 'database'])
            ->once()
            ->andReturn($channel);

        $mock = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('buildByDriver')
            ->with($channel, $notifiable)
            ->once();

        $mock->toDatabase($notifiable);
    }

    /**
     * @covers ::decideLocale
     */
    public function test_decideLocale_LocaleIsSet()
    {
        $notifiable = '';
        $mock = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->locale = 'notification_locale';
        $actual = $mock->decideLocale($notifiable);
        $this->assertSame('notification_locale', $actual);
    }

    /**
     * @covers ::decideLocale
     */
    public function test_decideLocale_NotifiableHasLocalePreference()
    {
        $stubNotifiable = \Mockery::mock(\stdClass::class, HasLocalePreference::class);
        $stubNotifiable->shouldReceive('preferredLocale')->once()->andReturn('notifiable_locale');

        $mock = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->locale = null;
        $actual = $mock->decideLocale($stubNotifiable);
        $this->assertSame('notifiable_locale', $actual);
    }

    /**
     * @covers ::decideLocale
     */
    public function test_decideLocale_Default()
    {
        $notifiable = '';
        $mock = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->locale = null;
        $mock->shouldReceive('getLaravelCommonConfig->getAppLocale')->once()->andReturn('app_locale');
        $actual = $mock->decideLocale($notifiable);
        $this->assertSame('app_locale', $actual);
    }

    /**
     * @covers ::buildByDriver
     */
    public function test_buildByDriver()
    {
        $stubNotifiable = '';
        $stubChannel = \Mockery::mock(NotificationChannel::class);

        $stubConfig = \Mockery::mock(NotificationTemplate::class);
        $stubConfig->shouldReceive('createDriverObject->build')->once()->andReturn('RESULT');

        $mock = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('decideLocale')->with($stubNotifiable)->once()->andReturn('locale');
        $mock->shouldReceive('getDtoObjects->getDtoObject')->with($stubChannel, 'locale')->once()->andReturn(
            new \stdClass()
        );
        $mock->shouldReceive('getSetting->getTemplate')->with($stubChannel, 'locale')->once()->andReturn($stubConfig);

        $actual = $mock->buildByDriver($stubChannel, $stubNotifiable);
        $this->assertSame('RESULT', $actual);
    }

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $argId = 999;

        $stubSetting = \Mockery::mock(NotificationSetting::class);

        $stubService = \Mockery::mock(NotificationSettingService::class);
        $stubService->shouldReceive('getById')->with($argId)->andReturn($stubSetting);

        $stubDtoObjects = \Mockery::mock(DtoObjects::class);

        $stubConfig = \Mockery::mock(LaravelCommonConfig::class);

        App::shouldReceive('make')->with(NotificationSettingService::class)->andReturn($stubService);
        App::shouldReceive('make')->with(DtoObjects::class, ['id' => $argId])->andReturn($stubDtoObjects);
        App::shouldReceive('make')->with(LaravelCommonConfig::class)->andReturn($stubConfig);


        $targetClass = new $this->testClassName($argId);
        $this->assertInstanceOf(ShouldQueue::class, $targetClass);
        $this->assertInstanceOf(Notification::class, $targetClass);

        return [$targetClass, $stubSetting, $stubDtoObjects, $stubConfig];
    }

    /**
     * @param $depends
     * @covers ::getDtoObjects
     * @depends test___construct
     */
    public function test_getDtoObjects($depends)
    {
        $targetClass = $depends[0];
        $stubDtoObjects = $depends[2];

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $stubDtoObjects) {
                $actual = $targetClass->getDtoObjects();
                //assertions
                $this->assertSame($stubDtoObjects, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @param $depends
     * @covers ::getSetting
     * @depends test___construct
     */
    public function test_getSetting($depends)
    {
        $targetClass = $depends[0];
        $stubSetting = $depends[1];

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $stubSetting) {
                $actual = $targetClass->getSetting();
                //assertions
                $this->assertSame($stubSetting, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @param $depends
     * @covers ::getLaravelCommonConfig
     * @depends test___construct
     */
    public function test_getLaravelCommonConfig($depends)
    {
        $targetClass = $depends[0];
        $stubConfig = $depends[3];

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $stubConfig) {
                $actual = $targetClass->getLaravelCommonConfig();
                //assertions
                $this->assertSame($stubConfig, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}
