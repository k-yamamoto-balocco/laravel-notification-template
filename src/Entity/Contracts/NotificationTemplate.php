<?php

namespace GitBalocco\LaravelNotificationTemplate\Entity\Contracts;

use ArrayAccess;
use GitBalocco\LaravelNotificationTemplate\Drivers\Contracts\ChannelDriver;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DriverName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DtoClassName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName;

interface NotificationTemplate extends ArrayAccess
{
    public function getId(): int;

    public function getChannel(): NotificationChannel;

    public function getViewName(): ViewName;

    public function getDtoClass(): DtoClassName;

    public function getLocale(): string;

    public function toArray(): array;

    public function getDriver(): DriverName;

    public function createDriverObject($dtoObject, $notifiable): ChannelDriver;
}
