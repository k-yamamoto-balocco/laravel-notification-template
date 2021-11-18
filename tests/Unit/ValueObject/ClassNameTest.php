<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject;

use GitBalocco\LaravelNotificationTemplate\ValueObject\ClassName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\StringValue;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\ValueObject\ClassName
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject\ClassNameTest
 */
class ClassNameTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ClassName::class;

    /**
     * @coversNothing
     */
    public function test___construct()
    {
        $targetClass = new $this->testClassName(\stdClass::class);
        $this->assertInstanceOf(StringValue::class, $targetClass);
    }

    /**
     * @covers ::setValue
     */
    public function test_setValue_RaiseExceptionWithEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        new $this->testClassName('');
    }

    /**
     * @covers ::setValue
     */
    public function test_setValue_RaiseExceptionWithInvalidClassName()
    {
        $this->expectException(InvalidArgumentException::class);
        new $this->testClassName('ClassNameDoesntExist');
    }

    /**
     * @covers ::setValue
     */
    public function test_setValue()
    {
        $targetClass = new $this->testClassName(\stdClass::class);
        $this->assertSame(\stdClass::class, $targetClass->getValue());
    }

    /**
     * @coversNothing
     */
    public function test_serialize()
    {
        $targetClass = new $this->testClassName(\stdClass::class);
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
