<?php

namespace GitBalocco\LaravelNotificationTemplate\Traits;

use GitBalocco\LaravelNotificationTemplate\Base;
use GitBalocco\LaravelNotificationTemplate\Entity\DtoObjects;
use GitBalocco\LaravelNotificationTemplate\Exceptions\TemplateNotFoundException;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use Illuminate\Support\Facades\App;

trait DtoObjectsTrait
{
    /** @var DtoObjects $dtoObjects */
    private $dtoObjects;

    /**
     * @param $dtoObject
     * @param string $channelName
     * @param string $locale
     * @throws TemplateNotFoundException
     */
    public function assign($dtoObject, string $channelName, string $locale): void
    {
        $notificationChannel = App::make(NotificationChannel::class, ['value' => $channelName]);
        $this->getDtoObjects()->setDtoObject($notificationChannel, $locale, $dtoObject);
    }

    /**
     * @return DtoObjects
     */
    private function getDtoObjects(): DtoObjects
    {
        return $this->dtoObjects;
    }

    /**
     * @param $dtoObject
     * @return void
     * @throws TemplateNotFoundException
     */
    public function assignToAll($dtoObject): void
    {
        $this->getDtoObjects()->setDtoObjectToAll($dtoObject);
    }

    /**
     * @param $dtoObject
     * @param string $channelName
     * @return void
     * @throws TemplateNotFoundException
     */
    public function assignToChannel($dtoObject, string $channelName): void
    {
        /** @var NotificationChannel $notificationChannel */
        $notificationChannel = App::make(NotificationChannel::class, ['value' => $channelName]);

        $this->getDtoObjects()->setDtoObjectToChannel($notificationChannel, $dtoObject);
    }

    /**
     * @param $dtoObject
     * @param string $locale
     * @return void
     * @throws TemplateNotFoundException
     */
    public function assignToLocale($dtoObject, string $locale): void
    {
        $this->getDtoObjects()->setDtoObjectToLocale($locale, $dtoObject);
    }
}
