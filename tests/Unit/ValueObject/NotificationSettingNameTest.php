<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject;

use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationSettingName;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationSettingName
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject\NotificationSettingNameTest
 */
class NotificationSettingNameTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = NotificationSettingName::class;

    /**
     * @covers ::setValue
     */
    public function test_setValue_RaiseException()
    {
        $this->expectException(InvalidArgumentException::class);
        new $this->testClassName('');
    }

    /**
     * @covers ::setValue
     */
    public function test_setValue()
    {
        $targetClass = new $this->testClassName('setting_name');
        $this->assertSame('setting_name', $targetClass->getValue());
    }

    /**
     * @coversNothing
     */
    public function test_serialize()
    {
        $targetClass = new $this->testClassName('setting_name');
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
