<?php


namespace GitBalocco\LaravelNotificationTemplate\Service\Command;


use Exception;
use GitBalocco\LaravelNotificationTemplate\Common\ArrayDirectProduct;
use GitBalocco\LaravelNotificationTemplate\Common\LaravelCommonConfig;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;
use GitBalocco\LaravelNotificationTemplate\Repository\Contracts\NotificationSettingRepository;
use GitBalocco\LaravelNotificationTemplate\Service\NotificationSettingService;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use Illuminate\Support\Facades\App;

/**
 * Class ConfigureCheckService
 * @package GitBalocco\LaravelNotificationTemplate\Service\Command
 */
class ConfigureCheckService
{
    /** @var NotificationSettingService|null $dataService */
    private $dataService;

    /** @var LaravelCommonConfig|null $laravelConfig */
    private $laravelConfig;

    /** @var CliMessages $messages */
    private $messages;

    /** @var NotificationSettingRepository $repository */
    private $repository;


    public function __construct()
    {
        $this->messages = App::make(CliMessages::class);
        $this->repository = App::make(NotificationSettingRepository::class);
    }

    /**
     * @return bool
     */
    public function checkLaravelCommonConfig(): bool
    {
        $this->getMessages()->add('line', '');
        $this->getMessages()->add('info', 'Start to check Laravel common configuration files.');
        try {
            $config = $this->makeLaravelCommonConfig();
            $this->getMessages()->add('line', '[OK] Items that depend on Laravel settings are as bellow');
            $this->getMessages()->add('line', 'app.locale:"' . $config->getAppLocale() . '"');
            $this->getMessages()->add('line', 'mail.from.address:"' . $config->getMailFromAddress() . '"');
            $this->getMessages()->add('line', 'mail.from.name:"' . $config->getMailFromName() . '"');
            return true;
        } catch (\Throwable $e) {
            $this->getMessages()->add('error', '[NG] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @return CliMessages
     */
    public function getMessages(): CliMessages
    {
        return $this->messages;
    }

    /**
     * Laravelの設定ファイルをロードしてオブジェクトを作成する。
     * ConfigCheckでは、「インスタンス化できるかどうか」がチェックの一種となるため、オブジェクト生成をメソッド化している。
     * @return LaravelCommonConfig
     */
    protected function makeLaravelCommonConfig(): LaravelCommonConfig
    {
        $laravelConfig = App::make(LaravelCommonConfig::class);
        $this->laravelConfig = $laravelConfig;
        return $laravelConfig;
    }

    /**
     * @return bool
     */
    public function checkMakeDataService(): bool
    {
        $this->getMessages()->add('line', '');
        $this->getMessages()->add('info', 'Start to check data source.');
        try {
            $repoClassName = $this->makeDataService();
            $this->getMessages()->add('line', '[OK] data source has been detected.');
            $this->getMessages()->add('line', 'Repository class is ' . $repoClassName);
            return true;
        } catch (\Throwable $e) {
            $this->getMessages()->add('error', '[NG] The configuration file is invalid. ' . $e->getMessage());
            return false;
        }
    }

    /**
     * データ取得サービスの作成
     * @return string class name of repository.
     */
    protected function makeDataService(): string
    {
        $this->dataService = App::make(NotificationSettingService::class);
        return $this->dataService->repositoryClassName();
    }

    /**
     * @return bool
     */
    public function canSettingsInstantiable(): bool
    {
        $this->getMessages()->add('line', '');
        $this->getMessages()->add('info', 'Start to check settings.');
        $notificationCount = 0;
        $errorCount = 0;
        $result = true;

        foreach ($this->getRepository()->all() as $arrSetting) {
            $notificationCount++;
            //インスタンス化を試みる
            try {
                /** @var NotificationSetting $setting */
                $setting = App::make(NotificationSetting::class, ['row' => $arrSetting]);
                $this->getMessages()->add('line', '[OK] notification :' . $arrSetting['id'] . ' ');
            } catch (\Throwable $e) {
                $this->getMessages()->add(
                    'error',
                    '[NG] notification :' . ($arrSetting['id'] ?? 'id undefined') . ' ' . $e->getMessage()
                );
                $errorCount++;
                $result = false;
            }
        }

        if ($errorCount > 0) {
            $this->getMessages()->add('error', '[NG] ' . $errorCount . " invalid notification have been detected.");
        } else {
            $this->getMessages()->add(
                'line',
                '[OK]' . $notificationCount . " notification settings have been detected."
            );
        }
        return $result;
    }

    /**
     * @return NotificationSettingRepository
     */
    protected function getRepository(): NotificationSettingRepository
    {
        return $this->repository;
    }

    /**
     * channel と locale の組み合わせがすべて登録されているか検証
     * @return int
     */
    public function checkSettingsChannelLocaleCombination(): int
    {
        $errorCount = 0;
        $this->getMessages()->add('line', '');
        $this->getMessages()->add(
            'info',
            'Start to make sure that the channel and locale combination is set correctly.'
        );

        //3配列の積を作って、全パターンに対してテスト
        /** @var NotificationSetting $setting */
        foreach ($this->settings() as $setting) {
            $errorCount += $this->checkCombinations($setting);
        }
        return $errorCount;
    }

    /**
     * @return iterable<NotificationSetting>
     * @throws Exception
     */
    public function settings(): iterable
    {
        if (is_null($this->dataService)) {
            throw new Exception('Data service is null.');
        }
        return $this->dataService->all();
    }

    /**
     * @param NotificationSetting $setting
     * @return int
     */
    protected function checkCombinations(NotificationSetting $setting)
    {
        $errorCount = 0;
        $combination = new ArrayDirectProduct($setting->channels(), $setting->locales());
        foreach ($combination as $item) {
            $errorCount += $this->tryGetViewName($setting, $item[0], $item[1]);
        }
        return $errorCount;
    }

    /**
     * @param NotificationSetting $setting
     * @param string $channel
     * @param string $locale
     * @return int
     */
    protected function tryGetViewName(NotificationSetting $setting, string $channel, string $locale)
    {
        $channel = new NotificationChannel($channel);

        try {
            $viewName = $setting->getViewName($channel, $locale);
            $this->getMessages()->add(
                'line',
                '[OK] notification :' . $setting->getId(
                ) . ' [channel:' . (string)$channel . '] [locale:' . $locale . '] [viewName:' . $viewName . ']'
            );
            return 0;
        } catch (\Throwable $e) {
            $this->getMessages()->add(
                'error',
                '[NG] notification :' . $setting->getId() . ' ' . $e->getMessage()
            );
            $this->getMessages()->add(
                'warn',
                'An exception may occur when notifying in the [locale:' . $locale . ']'
            );
            return 1;
        }
    }

    /**
     * @return int
     * @throws Exception
     */
    public function checkForAppLocale(): int
    {
        $this->getMessages()->add('line', '');
        $this->getMessages()->add('info', 'Start to make sure the template for app.locale is set.');

        $errorCount = 0;
        /** @var NotificationSetting $notificationSetting */
        foreach ($this->settings() as $setting) {
            $errorCount += $this->checkCombinationsAppLocale($setting);
        }

        return $errorCount;
    }

    /**
     * @param NotificationSetting $setting
     * @return int
     * @throws Exception
     */
    protected function checkCombinationsAppLocale(NotificationSetting $setting)
    {
        $errorCount = 0;
        $appLocale = $this->laravelConfig()->getAppLocale();
        $combination = new ArrayDirectProduct($setting->channels(), [$appLocale]);
        foreach ($combination as $item) {
            $errorCount += $this->tryGetViewName($setting, $item[0], $item[1]);
        }
        if ($errorCount > 0) {
            $this->getMessages()->add(
                'warn',
                'Change the value of config("app.locale") or add a template setting for locale "' .
                $appLocale . '".'
            );
        }
        return $errorCount;
    }

    /**
     * @return LaravelCommonConfig
     * @throws Exception
     */
    public function laravelConfig(): LaravelCommonConfig
    {
        if (is_null($this->laravelConfig)) {
            throw new Exception('Laravel config is null.');
        }
        return $this->laravelConfig;
    }

}