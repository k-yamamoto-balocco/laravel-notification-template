<?php

namespace GitBalocco\LaravelNotificationTemplate\DataTransferObject;

use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;

/**
 * Class AllSetting
 */
class AllSetting
{
    /** @var iterable<NotificationSetting> $all */
    private $settings;

    /**
     * AllSetting constructor.
     * @param iterable<NotificationSetting> $settings
     */
    public function __construct(iterable $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $rows = [];
        /** @var NotificationSetting $setting */
        foreach ($this->settings as $setting) {
            /** @var NotificationTemplate $template */
            foreach ($setting->getTemplates() as $template) {
                $row = [
                    $setting->getId(),
                    (string)$setting->getName(),
                    (string)$template->getId(),
                    (string)$template->getChannel(),
                    (string)$template->getLocale(),
                    (string)$template->getViewName(),
                    (string)$template->getDtoClass(),
                    (string)$template->getDriver(),
                ];
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function header(): array
    {
        return ['id', 'name', 'template_id', 'channel', 'locale', 'template', 'dto', 'driver'];
    }
}