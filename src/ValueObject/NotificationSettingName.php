<?php

namespace GitBalocco\LaravelNotificationTemplate\ValueObject;

use GitBalocco\LaravelNotificationTemplate\Exceptions\BadConfigurationException;
use InvalidArgumentException;

/**
 * Class NotificationSettingName
 * @package GitBalocco\LaravelNotificationTemplate\ValueObject
 */
class NotificationSettingName extends StringValue
{
    /**
     * @param string $value
     */
    protected function setValue(string $value): void
    {
        if ($value === '') {
            throw new InvalidArgumentException('通知名が空文字列');
        }
        $this->value = $value;
    }
}
