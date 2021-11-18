<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Service\Command;

use GitBalocco\LaravelNotificationTemplate\Service\Command\CliMessages;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Service\Command\CliMessages
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Service\Command\CliMessagesTest
 */
class CliMessagesTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = CliMessages::class;

    /**
     * @covers ::add
     * @covers ::getIterator
     */
    public function test_add()
    {
        $targetClass = new $this->testClassName();
        $targetClass->add('info', 'string-info-message');
        $targetClass->add('warn', 'string-warn-message');
        $array = iterator_to_array($targetClass);

        $this->assertSame('info', $array[0]->getStatus());
        $this->assertSame('string-info-message', $array[0]->getMessage());
        $this->assertSame('warn', $array[1]->getStatus());
        $this->assertSame('string-warn-message', $array[1]->getMessage());
    }
}
