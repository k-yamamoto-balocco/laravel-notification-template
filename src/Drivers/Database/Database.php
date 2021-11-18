<?php

namespace GitBalocco\LaravelNotificationTemplate\Drivers\Database;

use GitBalocco\LaravelNotificationTemplate\Drivers\Database\Contracts\DatabaseChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Drivers\Traits\ChannelDriverTrait;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\DefaultSetting;
use Illuminate\Support\Facades\View;
use Throwable;

/**
 * Class Database
 * @package GitBalocco\LaravelNotificationTemplate\Drivers\Database
 * @method DatabaseConfig getConfig() : NotificationTemplate
 */
class Database implements DatabaseChannelDriver
{
    use ChannelDriverTrait;

    /**
     * Database constructor.
     * @param DefaultSetting $config
     * @param mixed $dtoObject
     * @param mixed $notifiable
     */
    public function __construct(DefaultSetting $config, $dtoObject, $notifiable)
    {
        $this->config = $config;
        $this->dtoObject = $dtoObject;
        $this->notifiable = $notifiable;
    }


    /**
     * @return array
     * @throws Throwable
     */
    public function build(): array
    {
        /** @var \Illuminate\View\View $view */
        $view = View::make($this->getConfig()->getViewName(), ['dto' => $this->getDtoObject()]);

        return [
            'message' => $view->render(),
        ];
    }
}