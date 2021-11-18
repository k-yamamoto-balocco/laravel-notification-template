<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Drivers\Traits;

use GitBalocco\LaravelNotificationTemplate\Drivers\Traits\ChannelDriverTrait;
use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Drivers\Traits\ChannelDriverTrait
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Drivers\Traits\ChannelDriverTraitTest
 */
class ChannelDriverTraitTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ChannelDriverTrait::class;

    /**
     * @covers ::getConfig
     */
    public function test_getConfig()
    {
        $stubConfig = \Mockery::mock(\stdClass::class, NotificationTemplate::class);
        $stubDtoObject = new \stdClass();
        $notifiable='';
        $targetClass = $this->createObject($stubConfig, $stubDtoObject,$notifiable);
        $actual = $targetClass->getConfig();
        $this->assertSame($stubConfig, $actual);
    }

    /**
     * @param $config
     * @param $dtoObject
     * @param $notifiable
     * @return mixed
     */
    public function createObject($config, $dtoObject,$notifiable)
    {
        return
            new class($config, $dtoObject,$notifiable) {
                use ChannelDriverTrait;
                /**
                 *  constructor.
                 * @param $config
                 * @param $dtoObject
                 * @param $notifiable
                 */
                public function __construct($config, $dtoObject,$notifiable)
                {
                    $this->config = $config;
                    $this->dtoObject = $dtoObject;
                    $this->notifiable = $notifiable;
                }
            };
    }

    /**
     * @covers ::getDtoObject
     */
    public function test_getDtoObject()
    {
        $stubConfig = \Mockery::mock(\stdClass::class, NotificationTemplate::class);
        $stubDtoObject = new \stdClass();
        $notifiable='';

        $targetClass = $this->createObject($stubConfig, $stubDtoObject,$notifiable);
        $actual = $targetClass->getDtoObject();
        $this->assertSame($stubDtoObject, $actual);
    }

    /**
     * @covers ::getNotifiable
     */
    public function test_getNotifiable(){
        $stubConfig = \Mockery::mock(\stdClass::class, NotificationTemplate::class);
        $stubDtoObject = new \stdClass();
        $notifiable = new \stdClass();

        $targetClass = $this->createObject($stubConfig, $stubDtoObject,$notifiable);

        //テスト対象メソッドの実行
        $actual = $targetClass->getNotifiable();
        //assertions
        $this->assertSame($notifiable,$actual);
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
