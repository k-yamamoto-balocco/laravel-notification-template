<?php


namespace GitBalocco\LaravelNotificationTemplate\Entity;


use GitBalocco\LaravelNotificationTemplate\Common\ConfigurationChecker;
use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use GitBalocco\LaravelNotificationTemplate\Exceptions\TemplateNotFoundException;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ClassName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DriverName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationSettingName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 * Class Configuration
 * 設定1行相当のオブジェクト
 * @package GitBalocco\LaravelNotificationTemplate
 */
class NotificationSetting
{
    /** @var int $id 識別子 */
    private $id;
    /** @var NotificationSettingName $name 通知名 */
    private $name;
    /** @var NotificationTemplate[] $templates */
    private $templates = [];

    /**
     * NotificationSetting constructor.
     * @param array $row
     */
    public function __construct(array $row)
    {
        $checker = App::make(ConfigurationChecker::class, ['row' => $row]);
        $checker->check();

        $this->id = $row['id'];

        $this->name = App::make(NotificationSettingName::class, ['value' => $row['name']]);

        foreach ($row['notification_templates'] as $templatesSetting) {
            /** @var NotificationChannel $channel */
            $channel = App::make(NotificationChannel::class, ['value' => $templatesSetting['channel']]);
            $this->templates[] = $channel->notificationTemplateObject($templatesSetting);;
        }
    }

    /**
     * @return NotificationChannel[]
     */
    public function via(): array
    {
        return $this->channels();
    }

    /**
     * @return NotificationChannel[]
     */
    public function channels(): array
    {
        $result = [];
        /** @var Collection $collection */
        $collection = collect($this->getTemplates());

        foreach ($collection->pluck('channel') as $channel) {
            if ($channel) {
                $result[(string)$channel] = $channel;
            }
        }

        return array_values($result);


    }

    /**
     * @return NotificationTemplate[]
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    /**
     * @param NotificationChannel $channel
     * @param string $locale
     * @return ClassName
     * @throws TemplateNotFoundException
     */
    public function getDtoClass(NotificationChannel $channel, string $locale): ClassName
    {
        return $this->getTemplate($channel, $locale)->getDtoClass();
    }

    /**
     * @param NotificationChannel $channel
     * @param string $locale
     * @return NotificationTemplate
     * @throws TemplateNotFoundException
     */
    public function getTemplate(NotificationChannel $channel, string $locale): NotificationTemplate
    {
        $tmpCollection = App::make(Collection::class, ['items' => $this->getTemplates()]);

        $notificationTemplate = $tmpCollection
            ->where('channel', '=', (string)$channel)
            ->where('locale', '=', $locale)
            ->first();

        if (!is_null($notificationTemplate)) {
            return $notificationTemplate;
        }
        throw new TemplateNotFoundException($channel, $locale);
    }

    /**
     * @param NotificationChannel $channel
     * @param string $locale
     * @return ViewName
     * @throws TemplateNotFoundException
     */
    public function getViewName(NotificationChannel $channel, string $locale): ViewName
    {
        return $this->getTemplate($channel, $locale)->getViewName();
    }

    /**
     * @param NotificationChannel $channel
     * @param string $locale
     * @return DriverName
     * @throws TemplateNotFoundException
     */
    public function getDriverName(NotificationChannel $channel, string $locale): DriverName
    {
        return $this->getTemplate($channel, $locale)->getDriver();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return NotificationSettingName
     */
    public function getName(): NotificationSettingName
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function locales(): array
    {
        return collect($this->getTemplates())
            ->pluck('locale')
            ->unique()
            ->reject(function($name){return empty($name);})
            ->values()
            ->toArray();
    }

    public function combination(){

    }
}