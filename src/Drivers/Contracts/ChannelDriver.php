<?php

namespace GitBalocco\LaravelNotificationTemplate\Drivers\Contracts;

use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;

interface ChannelDriver
{
    /**
     * @return NotificationTemplate
     */
    public function getConfig(): NotificationTemplate;

    /**
     * @return mixed
     */
    public function getDtoObject();
}
