<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject;

use GitBalocco\LaravelNotificationTemplate\ValueObject\StringValue;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\ValueObject\StringValue
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject\StringValueTest
 */
class StringValueTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = StringValue::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $targetClass = new class('argument_string') extends StringValue {
            public $called = false;

            protected function setValue(string $value): void
            {
                $this->value = $value;
                $this->called = true;
            }
        };
        $this->assertTrue($targetClass->called);
    }

    /**
     * @covers ::__toString
     */
    public function test___toString()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        $targetClass->shouldReceive('getValue')->once();
        $targetClass->__toString();
    }

    /**
     * @covers ::equals
     */
    public function test_equals()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();

        $targetClass->shouldReceive('getValue')
            ->twice()
            ->andReturn('string_value');

        $this->assertTrue($targetClass->equals('string_value'));
        $this->assertFalse($targetClass->equals('dont_match'));
    }

    /**
     * @covers ::getValue
     */
    public function test_getValue()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        $this->assertSame('', $targetClass->getValue());
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
