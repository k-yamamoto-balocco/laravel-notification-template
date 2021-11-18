<?php

namespace GitBalocco\LaravelNotificationTemplate\ValueObject;

use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;

/**
 * Class NotificationChannel
 * @package GitBalocco\LaravelNotificationTemplate\ValueObject
 */
class NotificationChannel extends StringValue
{
    /** @var SupportedChannelList $channelList */
    private $channelList;

    /**
     * NotificationChannel constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->channelList = App::make(SupportedChannelList::class);
        parent::__construct($value);
    }

    /**
     * @param array $parameter
     * @return mixed
     */
    public function notificationTemplateObject(array $parameter): NotificationTemplate
    {
        $className = $this->channelList->configClassOf($this->getValue());
        return App::make($className, $parameter);
    }

    /**
     * チャンネルのデフォルトドライバ名を返す
     * @return string
     */
    public function defaultDriverClassName(): string
    {
        return $this->channelList->defaultDriverOf($this->getValue());
    }

    /**
     * チャンネルドライバが実装するべきインターフェース名を返す
     * @return string
     */
    public function driverInterfaceName(): string
    {
        return $this->channelList->driverInterfaceOf($this->getValue());
    }

    /**
     * @param string $value
     */
    protected function setValue(string $value): void
    {
        if (!$this->channelList->contains($value)) {
            throw new InvalidArgumentException('チャンネル名:' . $value . 'は未対応');
        }
        $this->value = $value;
    }

}