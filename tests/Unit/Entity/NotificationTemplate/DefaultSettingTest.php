<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Entity\NotificationTemplate;

use GitBalocco\LaravelNotificationTemplate\Drivers\Contracts\ChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\DefaultSetting;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DriverName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DtoClassName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\DefaultSetting
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Entity\NotificationTemplate\DefaultSettingTest
 */
class DefaultSettingTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = DefaultSetting::class;

    /**
     * @covers ::createDriverObject
     */
    public function test_createDriverObject()
    {
        $stubDriverName = \Mockery::mock(DriverName::class);
        $stubDriverName->shouldReceive('__toString')->andReturn('DriverName');
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        $targetClass->shouldReceive('getDriver')->once()->andReturn($stubDriverName);

        $stubResult = \Mockery::mock(ChannelDriver::class);

        $dtoObject = new \stdClass();
        $notifiable = new \stdClass();

        App::shouldReceive('make')->with(
            'DriverName',
            [
                'config' => $targetClass,
                'dtoObject' => $dtoObject,
                'notifiable' => $notifiable
            ]
        )->once()->andReturn($stubResult);

        //テスト対象メソッドの実行
        $targetClass->createDriverObject($dtoObject, $notifiable);
    }

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        //Arg
        $viewName = 'arg_view_name';
        $channel = 'arg_channel_name';
        $locale = 'arg_locale';
        $dtoClass = 'arg_dto_class';
        $driver = 'arg_driver';

        //Stub
        $stubViewName = \Mockery::mock(ViewName::class)->shouldIgnoreMissing()->makePartial();
        $stubNotificationChannel = \Mockery::mock(NotificationChannel::class)->shouldIgnoreMissing()->makePartial();
        $stubDtoClassName = \Mockery::mock(DtoClassName::class)->shouldIgnoreMissing()->makePartial();
        $stubDriverName = \Mockery::mock(DriverName::class)->shouldIgnoreMissing()->makePartial();


        App::shouldReceive('make')->with(ViewName::class, ['value' => $viewName])->once()->andReturn($stubViewName);
        App::shouldReceive('make')->with(NotificationChannel::class, ['value' => $channel])->once()->andReturn(
            $stubNotificationChannel
        );
        App::shouldReceive('make')->with(DtoClassName::class, ['value' => $dtoClass])->once()->andReturn(
            $stubDtoClassName
        );
        App::shouldReceive('make')->with(
            DriverName::class,
            ['value' => $driver, 'channel' => $stubNotificationChannel]
        )->once()->andReturn($stubDriverName);

        $targetClass = new $this->testClassName(9999, $viewName, $channel, $locale, $dtoClass, $driver);

        $this->assertInstanceOf(NotificationTemplate::class,$targetClass);

        return [$targetClass, $stubViewName, $stubNotificationChannel, $stubDtoClassName, $stubDriverName];
    }

    /**
     * @param $depends
     * @covers ::getId
     * @depends test___construct
     */
    public function test_getId($depends)
    {
        $targetClass = $depends[0];

        //テスト対象メソッドの実行
        $actual = $targetClass->getId();
        //assertions
        $this->assertSame(9999, $actual);
    }

    /**
     * @param $depends
     * @covers ::getChannel
     * @depends test___construct
     */
    public function test_getChannel($depends)
    {
        $targetClass = $depends[0];
        $channel = $depends[2];

        //テスト対象メソッドの実行
        $actual = $targetClass->getChannel();
        //assertions
        $this->assertSame($channel, $actual);
    }

    /**
     * @param $depends
     * @covers ::getViewName
     * @depends test___construct
     */
    public function test_getViewName($depends)
    {
        $targetClass = $depends[0];
        $viewName = $depends[1];

        //テスト対象メソッドの実行
        $actual = $targetClass->getViewName();
        //assertions
        $this->assertSame($viewName, $actual);
    }

    /**
     * @param $depends
     * @covers ::getDtoClass
     * @depends test___construct
     */
    public function test_getDtoClass($depends)
    {
        $targetClass = $depends[0];
        $dtoClass = $depends[3];
        //テスト対象メソッドの実行
        $actual = $targetClass->getDtoClass();
        //assertions
        $this->assertSame($dtoClass, $actual);
    }

    /**
     * @param $depends
     * @covers ::getLocale
     * @depends test___construct
     */
    public function test_getLocale($depends)
    {
        $targetClass = $depends[0];
        //テスト対象メソッドの実行
        $actual = $targetClass->getLocale();
        //assertions
        $this->assertSame('arg_locale', $actual);
    }

    /**
     * @param mixed $depends
     * @covers ::getDriver
     * @depends test___construct
     */
    public function test_getDriver($depends)
    {
        $targetClass = $depends[0];
        $driverName = $depends[4];

        //テスト対象メソッドの実行
        $actual = $targetClass->getDriver();
        //assertions
        $this->assertSame($driverName, $actual);
    }



    /**
     *
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}
