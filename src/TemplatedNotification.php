<?php

namespace GitBalocco\LaravelNotificationTemplate;

use GitBalocco\LaravelNotificationTemplate\Common\LaravelCommonConfig;
use GitBalocco\LaravelNotificationTemplate\Drivers\DriverFactory;
use GitBalocco\LaravelNotificationTemplate\Entity\DtoObjects;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;
use GitBalocco\LaravelNotificationTemplate\Exceptions\TemplateNotFoundException;
use GitBalocco\LaravelNotificationTemplate\Service\NotificationSettingService;
use GitBalocco\LaravelNotificationTemplate\Traits\DtoObjectsTrait;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\NotificationSender;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

/**
 * Class TemplatedNotification
 * Laravelの通知まわりとつなぎこむ処理はすべてこのクラスに書かないと
 * @package GitBalocco\LaravelNotificationTemplate
 */
class TemplatedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    use DtoObjectsTrait;

    /** @var NotificationSetting $setting */
    private $setting;
    /** @var LaravelCommonConfig $laravelCommonConfig */
    private $laravelCommonConfig;

    /**
     * TemplatedNotification constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        /** @var NotificationSettingService $dataService */
        $dataService = App::make(NotificationSettingService::class);
        $this->setting = $dataService->getById($id);
        $this->dtoObjects = App::make(DtoObjects::class, ['id' => $id]);
        $this->laravelCommonConfig = App::make(LaravelCommonConfig::class);
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $result = [];
        //settingで定義されている情報から、自動的にチャンネルを決定する
        foreach ($this->getSetting()->via() as $channel) {
            $result[] = $channel->getValue();
        }
        return $result;
    }

    /**
     * @return NotificationSetting
     */
    protected function getSetting(): NotificationSetting
    {
        return $this->setting;
    }

    /**
     * @param $notifiable
     * @return mixed
     * @throws Exceptions\TemplateNotFoundException
     */
    public function toMail($notifiable)
    {
        return $this->buildByDriver(
            App::make(NotificationChannel::class, ['value' => 'mail']),
            $notifiable
        );
    }

    /**
     * @param NotificationChannel $channel
     * @param $notifiable
     * @return mixed
     * @throws TemplateNotFoundException
     */
    protected function buildByDriver(NotificationChannel $channel, $notifiable)
    {
        $locale = $this->decideLocale($notifiable);
        $dtoObject = $this->getDtoObjects()->getDtoObject($channel, $locale);
        $config = $this->getSetting()->getTemplate($channel, $locale);
        $driver = $config->createDriverObject($dtoObject, $notifiable);
        return $driver->build();
    }

    /**
     * @param $notifiable
     * @return string|null
     * @see NotificationSender::preferredLocale()
     */
    protected function decideLocale($notifiable)
    {
        return $this->locale ?? value(
            function () use ($notifiable) {
                if ($notifiable instanceof HasLocalePreference) {
                    return $notifiable->preferredLocale();
                }
            }
        ) ?? $this->getLaravelCommonConfig()->getAppLocale();
    }

    /**
     * @return LaravelCommonConfig
     */
    protected function getLaravelCommonConfig(): LaravelCommonConfig
    {
        return $this->laravelCommonConfig;
    }

    /**
     * @return mixed
     */
    protected function getDtoObjects()
    {
        return $this->dtoObjects;
    }

    /**
     * @param $notifiable
     * @return mixed
     * @throws Exceptions\TemplateNotFoundException
     */
    public function toDatabase($notifiable)
    {
        return $this->buildByDriver(
            App::make(NotificationChannel::class, ['value' => 'database']),
            $notifiable
        );
    }
}
