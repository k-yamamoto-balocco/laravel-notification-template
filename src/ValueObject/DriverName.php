<?php

namespace GitBalocco\LaravelNotificationTemplate\ValueObject;

use InvalidArgumentException;

/**
 * Class DriverName
 * @package GitBalocco\LaravelNotificationTemplate\ValueObject
 */
class DriverName extends ClassName
{
    /** @var NotificationChannel $channel */
    private $channel;

    /**
     * DriverName constructor.
     * @param NotificationChannel $channel
     * @param string $value
     */
    public function __construct(NotificationChannel $channel, string $value = '')
    {
        $this->channel = $channel;
        parent::__construct($value);
    }

    /**
     * @param string $class
     * @throws InvalidArgumentException
     */
    public function setValue(string $class): void
    {
        if ($class === '' || is_null($class)) {
            $class = $this->channel->defaultDriverClassName();
        }

        $interface = $this->channel->driverInterfaceName();
        if (!is_a($class, $interface, true)) {
            $message = 'Driver for "' . (string)$this->channel .
                '" channel must implements interface ' . $interface . '. ';
            $message .= 'Actually specified class is :' . $class;
            throw new InvalidArgumentException($message);
        }
        parent::setValue($class);
    }
}
