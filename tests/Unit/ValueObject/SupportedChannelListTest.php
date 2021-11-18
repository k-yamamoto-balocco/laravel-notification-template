<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject;

use GitBalocco\KeyValueList\Contracts\Definer;
use GitBalocco\KeyValueList\LaravelCacheClassification;
use GitBalocco\LaravelNotificationTemplate\ValueObject\SupportedChannelList;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\ValueObject\SupportedChannelList
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject\SupportedChannelListTest
 */
class SupportedChannelListTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = SupportedChannelList::class;

    /**
     * @coversNothing
     */
    public function test___construct()
    {
        $targetClass = new $this->testClassName();
        $this->assertInstanceOf(LaravelCacheClassification::class, $targetClass);
    }

    /**
     * @covers ::getDefiner
     */
    public function test_getDefiner()
    {
        $targetClass = new $this->testClassName();
        $actual = $targetClass->getDefiner();
        $this->assertInstanceOf(Definer::class, $actual);
    }

    /**
     * @covers ::defaultDriverOf
     */
    public function test_defaultDriverOf()
    {
        $argIdentity = 'mail';
        $targetClass = \Mockery::mock($this->testClassName)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $targetClass->shouldReceive('valueOf')
            ->with('defaultDriver', $argIdentity)
            ->once()
            ->andreturn('mail-defaultDriver');
        $actual = $targetClass->defaultDriverOf($argIdentity);
        $this->assertSame('mail-defaultDriver', $actual);
    }

    /**
     * @covers ::driverInterfaceOf
     */
    public function test_driverInterfaceOf(){
        $argIdentity = 'mail';
        $targetClass = \Mockery::mock($this->testClassName)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $targetClass->shouldReceive('valueOf')
            ->with('driverInterface', $argIdentity)
            ->once()
            ->andreturn('mail-driverInterface');
        $actual = $targetClass->driverInterfaceOf($argIdentity);
        $this->assertSame('mail-driverInterface', $actual);
    }

    /**
     * @covers ::configClassOf
     */
    public function test_configClassOf(){
        $argIdentity = 'mail';
        $targetClass = \Mockery::mock($this->testClassName)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $targetClass->shouldReceive('valueOf')
            ->with('configClass', $argIdentity)
            ->once()
            ->andreturn('mail-configClass');
        $actual = $targetClass->configClassOf($argIdentity);
        $this->assertSame('mail-configClass', $actual);
    }

    /**
     * @coversNothing
     */
    public function test_serialize()
    {
        $targetClass = new $this->testClassName();

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
