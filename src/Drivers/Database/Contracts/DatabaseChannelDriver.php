<?php

namespace GitBalocco\LaravelNotificationTemplate\Drivers\Database\Contracts;

use GitBalocco\LaravelNotificationTemplate\Drivers\Contracts\ChannelDriver;

interface DatabaseChannelDriver extends ChannelDriver
{
    public function build(): array;
}
