<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject;

use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use GitBalocco\LaravelNotificationTemplate\ValueObject\SupportedChannelList;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject\NotificationChannelTest
 */
class NotificationChannelTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = NotificationChannel::class;

    /**
     * @covers ::setValue
     * @covers ::__construct
     */
    public function test_setValue()
    {
        $stubList = \Mockery::mock(SupportedChannelList::class)->shouldIgnoreMissing();
        $stubList->shouldReceive('contains')->andReturnTrue();
        App::shouldReceive('make')->with(SupportedChannelList::class)->once()->andReturn($stubList);
        $targetClass = new $this->testClassName('hoge');
        $this->assertSame('hoge', $targetClass->getValue());
    }

    /**
     * @covers ::setValue
     * @covers ::__construct
     */
    public function test_setValue_RaiseException()
    {
        $stubList = \Mockery::mock(SupportedChannelList::class)->shouldIgnoreMissing();
        $stubList->shouldReceive('contains')->andReturnFalse();
        App::shouldReceive('make')->with(SupportedChannelList::class)->once()->andReturn($stubList);
        $this->expectException(InvalidArgumentException::class);
        new $this->testClassName('hoge');
    }

    /**
     * @covers ::defaultDriverClassName
     */
    public function test_defaultDriverClassName()
    {
        $stubList = \Mockery::mock(SupportedChannelList::class)
            ->shouldIgnoreMissing()
            ->makePartial();

        $stubList->shouldReceive('defaultDriverOf')
            ->with('mail')
            ->once()
            ->andReturn('mail-default-driver-name');

        App::shouldReceive('make')
            ->with(SupportedChannelList::class)
            ->andReturn($stubList);

        $targetClass = new $this->testClassName('mail');
        $actual = $targetClass->defaultDriverClassName();
        $this->assertSame('mail-default-driver-name', $actual);
    }

    /**
     * @covers ::driverInterfaceName
     */
    public function test_driverInterfaceName()
    {
        $stubList = \Mockery::mock(SupportedChannelList::class)
            ->shouldIgnoreMissing()
            ->makePartial();

        $stubList->shouldReceive('driverInterfaceOf')
            ->with('database')
            ->once()
            ->andReturn('database-driver-interface');

        App::shouldReceive('make')
            ->with(SupportedChannelList::class)
            ->andReturn($stubList);

        $targetClass = new $this->testClassName('database');
        $actual = $targetClass->driverInterfaceName();
        $this->assertSame('database-driver-interface', $actual);
    }

    /**
     * @covers ::notificationTemplateObject
     */
    public function test_notificationTemplateObject()
    {
        $argParameter = ['param' => 'value'];
        $stubList = \Mockery::mock(SupportedChannelList::class)
            ->shouldIgnoreMissing()
            ->makePartial();

        $stubList->shouldReceive('configClassOf')
            ->with('mail')
            ->once()
            ->andReturn('mail-channel-config-class');

        $stubNotificationTemplate = \Mockery::mock(NotificationTemplate::class);

        App::shouldReceive('make')
            ->with(SupportedChannelList::class)
            ->twice()
            ->andReturn($stubList);

        App::shouldReceive('make')
            ->with('mail-channel-config-class', $argParameter)
            ->once()
            ->andReturn($stubNotificationTemplate);

        $targetClass = new $this->testClassName('mail');
        $actual = $targetClass->notificationTemplateObject($argParameter);
        $this->assertSame($stubNotificationTemplate, $actual);
    }


    /**
     * @coversNothing
     */
    public function test_serialize()
    {
        $targetClass = new $this->testClassName('mail');
        try {
            serialize($targetClass);
            //OK
            $this->assertTrue(true);
        } catch (\Throwable $e) {
            //必ず失敗するAssertion
            $this->assertTrue(false, $e->getMessage());
        }
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }
}
