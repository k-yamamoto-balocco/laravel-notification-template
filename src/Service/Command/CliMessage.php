<?php


namespace GitBalocco\LaravelNotificationTemplate\Service\Command;

/**
 * Class CliMessage
 * @package GitBalocco\LaravelNotificationTemplate\Command
 */
class CliMessage
{
    /** @var string $status */
    private $status;
    /** @var string $message */
    private $message;

    /**
     * CliMessage constructor.
     * @param string $status
     * @param string $message
     */
    public function __construct(string $status, string $message)
    {
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }


}