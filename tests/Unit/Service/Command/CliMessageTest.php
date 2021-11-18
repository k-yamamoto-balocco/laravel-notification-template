<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Service\Command;

use GitBalocco\LaravelNotificationTemplate\Service\Command\CliMessage;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Service\Command\CliMessage
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Service\Command\CliMessageTest
 */
class CliMessageTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = CliMessage::class;

    /**
     * @covers ::__construct
     * @author kenji yamamoto <k.yamamoto@balocco.info>
     */
    public function test___construct()
    {
        $targetClass = new $this->testClassName('status', 'message');
        $this->assertInstanceOf(CliMessage::class, $targetClass);
        return $targetClass;
    }

    /**
     * @param mixed $targetClass
     * @covers ::getMessage
     * @depends test___construct
     * @author kenji yamamoto <k.yamamoto@balocco.info>
     */
    public function test_getMessage($targetClass)
    {
        //テスト対象メソッドの実行
        $actual = $targetClass->getMessage();
        //assertions
        $this->assertSame('message', $actual);
    }

    /**
     * @param mixed $targetClass
     * @covers ::getStatus
     * @depends test___construct
     * @author kenji yamamoto <k.yamamoto@balocco.info>
     */
    public function test_getStatus($targetClass)
    {
        //テスト対象メソッドの実行
        $actual = $targetClass->getStatus();
        //assertions
        $this->assertSame('status', $actual);
    }
}
