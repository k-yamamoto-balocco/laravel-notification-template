<?php

namespace GitBalocco\LaravelNotificationTemplate\ValueObject;

/**
 * Class StringValue
 * @package GitBalocco\LaravelNotificationTemplate\ValueObject
 */
abstract class StringValue
{
    /** @var string $value */
    protected $value = '';

    /**
     * StringValue constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return void
     * @codeCoverageIgnore
     */
    abstract protected function setValue(string $value): void;

    /**
     * @param string $value
     * @return bool
     */
    public function equals(string $value): bool
    {
        return ($this->getValue() === $value);
    }
}
