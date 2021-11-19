<?php

namespace GitBalocco\LaravelNotificationTemplate\Service\Command;

use ArrayIterator;
use IteratorAggregate;

/**
 * Class CliMessages
 * Service からCommandにメッセージを渡す際にこのオブジェクトを介する
 * @package GitBalocco\LaravelNotificationTemplate\Command
 */
class CliMessages implements IteratorAggregate
{
    private $messages = [];

    /**
     * @param string $status
     * @param string $message
     */
    public function add(string $status, string $message)
    {
        $this->messages[] = new CliMessage($status, $message);
    }

    /**
     * @return ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->messages);
    }
}
