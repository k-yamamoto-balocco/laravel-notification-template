<?php


namespace GitBalocco\LaravelNotificationTemplate\Entity;

use GitBalocco\LaravelNotificationTemplate\Exceptions\DtoMismatchException;
use GitBalocco\LaravelNotificationTemplate\Exceptions\TemplateNotFoundException;
use GitBalocco\LaravelNotificationTemplate\Service\NotificationSettingService;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 * Class DtoObjects
 * テンプレートに渡すべきオブジェクトを保持する。
 * @package GitBalocco\LaravelNotificationTemplate\Entity
 */
class DtoObjects
{
    /** @var Collection $objects */
    private $objects;
    /** @var NotificationSetting $setting */
    private $setting;

    /**
     * DtoObjects constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->objects = App::make(Collection::class);

        /** @var NotificationSettingService $service */
        $service = App::make(NotificationSettingService::class);

        $this->setting = $service->getById($id);
    }

    /**
     * チャンネルに対して一括セット
     * @param NotificationChannel $channel
     * @param $dtoObject
     * @return $this
     * @throws TemplateNotFoundException
     */
    public function setDtoObjectToChannel(NotificationChannel $channel, $dtoObject): DtoObjects
    {
        foreach ($this->setting->getTemplates() as $notificationTemplate) {
            if ($channel->equals($notificationTemplate->getChannel())) {
                $this->setDtoObject(
                    $notificationTemplate->getChannel(),
                    $notificationTemplate->getLocale(),
                    $dtoObject
                );
            }
        }
        return $this;
    }

    /**
     * @param NotificationChannel $channel
     * @param string $locale
     * @param $dtoObject
     * @return $this
     * @throws TemplateNotFoundException|DtoMismatchException
     */
    public function setDtoObject(NotificationChannel $channel, string $locale, $dtoObject): DtoObjects
    {
        $dtoClass = $this->getSetting()->getDtoClass($channel, $locale);
        if (!is_a($dtoObject, $dtoClass)) {
            throw new DtoMismatchException();
        }
        //cloneしたオブジェクトを保持
        $clone = clone $dtoObject;

        $this->objects->put($this->toKey($channel, $locale), $clone);
        return $this;
    }

    /**
     * @return NotificationSetting
     */
    private function getSetting(): NotificationSetting
    {
        return $this->setting;
    }

    /**
     * @param NotificationChannel $channel
     * @param string $locale
     * @return string
     */
    private function toKey(NotificationChannel $channel, string $locale): string
    {
        return (string)$channel . '.' . $locale;
    }

    /**
     * Localeに対してまとめてセット
     * @param string $locale
     * @param $dtoObject
     * @return $this
     * @throws TemplateNotFoundException
     */
    public function setDtoObjectToLocale(string $locale, $dtoObject): DtoObjects
    {
        foreach ($this->setting->getTemplates() as $notificationTemplate) {
            //localeが一致した場合のみアサイン
            if ($notificationTemplate->getLocale() === $locale) {
                $this->setDtoObject(
                    $notificationTemplate->getChannel(),
                    $notificationTemplate->getLocale(),
                    $dtoObject
                );
            }
        }
        return $this;
    }

    /**
     * 全パターンにまとめてセット
     * @param $dtoObject
     * @return $this
     * @throws TemplateNotFoundException
     */
    public function setDtoObjectToAll($dtoObject): DtoObjects
    {
        foreach ($this->setting->getTemplates() as $notificationTemplate) {
            $this->setDtoObject(
                $notificationTemplate->getChannel(),
                $notificationTemplate->getLocale(),
                $dtoObject
            );
        }
        return $this;
    }


    /**
     * @param NotificationChannel $channel
     * @param string $locale
     * @return mixed
     */
    public function getDtoObject(NotificationChannel $channel, string $locale)
    {
        return $this->objects->get($this->toKey($channel, $locale));
    }
}