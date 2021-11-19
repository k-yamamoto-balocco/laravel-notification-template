<?php

namespace GitBalocco\LaravelNotificationTemplate;

use GitBalocco\LaravelNotificationTemplate\Command\ConfigurationCheck;
use GitBalocco\LaravelNotificationTemplate\Command\TemplateList;
use Illuminate\Support\ServiceProvider as BaseProvider;

/**
 * Class ServiceProvider
 * @package GitBalocco\LaravelNotificationTemplate
 */
class ServiceProvider extends BaseProvider
{
    public function boot()
    {
        //コマンドの登録
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    TemplateList::class,
                    ConfigurationCheck::class,
                ]
            );
        }
        $this->publishes(
            [
                __DIR__ . 'config_example.php' => config_path('notification-template.php'),
            ]
        );
    }

    public function register()
    {
        //設定ファイル一択。将来的にDatabaseに保存できるようにするかもしれないが・・・
        $this->app->bind(
            \GitBalocco\LaravelNotificationTemplate\Repository\Contracts\NotificationSettingRepository::class,
            \GitBalocco\LaravelNotificationTemplate\Repository\ConfigFile\NotificationSettingRepository::class
        );
    }
}
