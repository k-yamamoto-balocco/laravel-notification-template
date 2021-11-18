<?php

namespace GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts;

interface HasSubject
{
    public function subject(): string;
}