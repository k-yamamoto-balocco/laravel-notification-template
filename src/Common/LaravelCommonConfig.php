<?php

namespace GitBalocco\LaravelNotificationTemplate\Common;

use GitBalocco\LaravelNotificationTemplate\ValueObject\RfcValidMailAddress;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * Class LaravelCommonConfig
 * Laravelのconfigに依存している箇所はこのクラスに集めておく
 * @package GitBalocco\LaravelNotificationTemplate\Common
 */
class LaravelCommonConfig
{
    /** @var string $appLocale refers to app.locale */
    private $appLocale;
    /** @var RfcValidMailAddress $mailFromAddress refers to mail.from.address */
    private $mailFromAddress;
    /** @var string $mailFromName refers to mail.from.address */
    private $mailFromName;

    /**
     * LaravelCommonConfig constructor.
     */
    public function __construct()
    {
        $this->appLocale = Config::get('app.locale');

        $this->mailFromAddress = App::make(
            RfcValidMailAddress::class,
            ['value' => Config::get('mail.from.address')]
        );

        $this->mailFromName = Config::get('mail.from.name');
    }

    /**
     * @return string
     */
    public function getAppLocale(): string
    {
        return $this->appLocale;
    }

    /**
     * @return RfcValidMailAddress
     */
    public function getMailFromAddress(): RfcValidMailAddress
    {
        return $this->mailFromAddress;
    }

    /**
     * @return string
     */
    public function getMailFromName(): string
    {
        return $this->mailFromName;
    }

    /**
     * @param string $path
     * @return string
     * @codeCoverageIgnore
     */
    public function getConfigPath(string $path): string
    {
        return config_path($path);
    }
}
