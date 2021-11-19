<?php

namespace GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts;

use GitBalocco\LaravelNotificationTemplate\ValueObject\MailFrom;

interface HasFrom
{
    public function from(): MailFrom;
}
