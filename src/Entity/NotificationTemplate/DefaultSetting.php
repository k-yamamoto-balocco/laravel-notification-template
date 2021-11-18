<?php


namespace GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate;

use GitBalocco\LaravelNotificationTemplate\Drivers\Contracts\ChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DriverName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DtoClassName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName;
use Gugunso\ReadOnlyObject\ReadOnlyObject;
use Illuminate\Support\Facades\App;

/**
 * Class NotificationTemplate
 * @package GitBalocco\LaravelNotificationTemplate\Entity
 */
class DefaultSetting extends ReadOnlyObject implements NotificationTemplate
{
    protected $id;
    /** @var ViewName $viewName テンプレートファイル名 */
    protected $viewName;
    /** @var NotificationChannel $channel このテンプレートの送信に利用されるチャンネル名 */
    protected $channel;
    /** @var DtoClassName $dtoClass テンプレートにアサインされるオブジェクトのクラス名 */
    protected $dtoClass;
    /** @var string $locale 言語 */
    protected $locale;
    /** @var DriverName $driver */
    protected $driver;

    /**
     * Base constructor.
     * @param $id
     * @param string $viewName
     * @param string $channel
     * @param string $dtoClass
     * @param string $locale
     * @param string $driver
     */
    public function __construct(
        $id,
        string $viewName,
        string $channel,
        string $locale,
        string $dtoClass = '',
        string $driver = ''
    ) {
        $this->id = $id;
        $this->viewName = App::make(ViewName::class, ['value' => $viewName]);
        $this->channel = App::make(NotificationChannel::class, ['value' => $channel]);
        $this->dtoClass = App::make(DtoClassName::class, ['value' => $dtoClass]);
        $this->driver = App::make(DriverName::class, ['value' => $driver, 'channel' => $this->channel]);
        $this->locale = $locale;
    }

    /**
     * @param $dtoObject
     * @param $notifiable
     * @return ChannelDriver
     */
    public function createDriverObject($dtoObject, $notifiable): ChannelDriver
    {
        $driverName = $this->getDriver();
        return App::make(
            (string)$driverName,
            [
                'config' => $this,
                'dtoObject' => $dtoObject,
                'notifiable' => $notifiable
            ]
        );
    }

    /**
     * @return DriverName
     */
    public function getDriver(): DriverName
    {
        return $this->driver;
    }

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return NotificationChannel
     */
    public function getChannel(): NotificationChannel
    {
        return $this->channel;
    }

    /**
     * @return ViewName
     */
    public function getViewName(): ViewName
    {
        return $this->viewName;
    }

    /**
     * @return DtoClassName
     */
    public function getDtoClass(): DtoClassName
    {
        return $this->dtoClass;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

}