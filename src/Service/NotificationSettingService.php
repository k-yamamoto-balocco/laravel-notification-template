<?php

namespace GitBalocco\LaravelNotificationTemplate\Service;

use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;
use GitBalocco\LaravelNotificationTemplate\Repository\Contracts\NotificationSettingRepository;
use Illuminate\Support\Facades\App;

class NotificationSettingService
{
    /** @var NotificationSettingRepository $repository */
    private $repository;

    /**
     * NotificationSettingService constructor.
     */
    public function __construct()
    {
        $this->repository = App::make(NotificationSettingRepository::class);
    }

    /**
     * @return iterable<NotificationSetting>
     */
    public function all(): iterable
    {
        foreach ($this->repository->all() as $arrSetting) {
            yield App::make(NotificationSetting::class, ['row' => $arrSetting]);
        }
    }

    /**
     * @param int $id
     * @return NotificationSetting
     */
    public function getById(int $id): NotificationSetting
    {
        return App::make(NotificationSetting::class, ['row' => $this->repository->getById($id)]);
    }

    /**
     * @return string
     */
    public function repositoryClassName(): string
    {
        return get_class($this->repository);
    }
}
