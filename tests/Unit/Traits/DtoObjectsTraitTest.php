<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Traits;

use GitBalocco\LaravelNotificationTemplate\Entity\DtoObjects;
use GitBalocco\LaravelNotificationTemplate\Traits\DtoObjectsTrait;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Traits\DtoObjectsTrait
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Traits\DtoObjectsTraitTest
 */
class DtoObjectsTraitTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = DtoObjectsTrait::class;

    /**
     * @covers ::assign
     */
    public function test_assign()
    {
        $argDto = new \stdClass();
        $argChannelName = 'channel';
        $argLocale = 'locale';

        $stubChannel = \Mockery::mock(NotificationChannel::class);

        App::shouldReceive('make')
            ->with(NotificationChannel::class, ['channel' => $argChannelName])
            ->andReturn($stubChannel);

        $stubDtoObjects = \Mockery::mock(DtoObjects::class);
        $stubDtoObjects->shouldReceive('setDtoObject')
            ->with($stubChannel, $argLocale, $argDto)
            ->once();

        $targetClass = $this->targetClass($stubDtoObjects);

        $targetClass->assign($argDto, $argChannelName, $argLocale);
    }

    public function targetClass($dtoObjects)
    {
        return new class($dtoObjects) {
            use DtoObjectsTrait;

            public function __construct($dtoObjects)
            {
                $this->dtoObjects = $dtoObjects;
            }
        };
    }

    /**
     * @covers ::assignToAll
     */
    public function test_assignToAll()
    {
        $argDto = new \stdClass();

        $stubDtoObjects = \Mockery::mock(DtoObjects::class);
        $stubDtoObjects->shouldReceive('setDtoObjectToAll')
            ->with($argDto)
            ->once();

        $targetClass = $this->targetClass($stubDtoObjects);
        $targetClass->assignToAll($argDto);
    }

    /**
     * @covers ::assignToChannel
     */
    public function test_assignToChannel()
    {
        $argChannelName = 'channel';
        $argDto = new \stdClass();

        $stubChannel = \Mockery::mock(NotificationChannel::class);

        $stubDtoObjects = \Mockery::mock(DtoObjects::class);
        $stubDtoObjects->shouldReceive('setDtoObjectToChannel')
            ->with($stubChannel, $argDto)
            ->once();

        App::shouldReceive('make')
            ->with(NotificationChannel::class, ['channel' => $argChannelName])
            ->andReturn($stubChannel);

        //テスト対象メソッドの実行
        $targetClass = $this->targetClass($stubDtoObjects);
        $targetClass->assignToChannel($argDto, $argChannelName);
    }

    /**
     * @covers ::assignToLocale
     */
    public function test_assignToLocale()
    {
        $argDto = new \stdClass();
        $argLocale = 'locale';

        $stubDtoObjects = \Mockery::mock(DtoObjects::class);
        $stubDtoObjects->shouldReceive('setDtoObjectToLocale')
            ->with($argLocale, $argDto)
            ->once();

        //テスト対象メソッドの実行
        $targetClass = $this->targetClass($stubDtoObjects);
        $targetClass->assignToLocale($argDto, $argLocale);
    }

    /**
     * @covers ::getDtoObjects
     */
    public function test_getDtoObjects()
    {
        $stubDtoObjects = \Mockery::mock(DtoObjects::class);
        /** @var mixed $targetClass */
        $targetClass = $this->targetClass($stubDtoObjects);

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

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}
