<?php


namespace GitBalocco\LaravelNotificationTemplate\Exceptions;

use Exception;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use Throwable;

class TemplateNotFoundException extends Exception
{
    public function __construct(
        NotificationChannel $channel,
        string $locale,
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message='NotificationTemplate not found. [channel :'.$channel.'] [locale:'.$locale.'] '.$message;
        parent::__construct($message, $code, $previous);
    }


}