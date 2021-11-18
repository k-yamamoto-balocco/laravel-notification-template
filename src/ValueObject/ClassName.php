<?php

namespace GitBalocco\LaravelNotificationTemplate\ValueObject;

use InvalidArgumentException;

/**
 * Class ClassName
 * @package GitBalocco\LaravelNotificationTemplate\ValueObject
 */
class ClassName extends StringValue
{
    /**
     * @param string $class
     */
    public function setValue(string $class): void
    {
        if ($class === "") {
            throw new InvalidArgumentException('空文字列をクラス名として指定することはできません。');
        }

        if (!class_exists($class)) {
            throw new InvalidArgumentException($class . ' は存在しません。');
        }
        $this->value = $class;
    }
}