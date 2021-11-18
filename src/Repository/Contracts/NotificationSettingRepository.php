<?php

namespace GitBalocco\LaravelNotificationTemplate\Repository\Contracts;

use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;

/**
 * Interface NotificationSettingRepository
 * @package GitBalocco\LaravelNotificationTemplate\Repository
 */
interface NotificationSettingRepository
{
    /**
     * @param int $id
     * @return array
     */
    public function getById(int $id): array;

    /**
     * @return iterable<NotificationSetting>
     */
    public function all(): iterable;
}