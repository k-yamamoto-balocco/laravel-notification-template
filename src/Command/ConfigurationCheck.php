<?php

namespace GitBalocco\LaravelNotificationTemplate\Command;

use Exception;
use GitBalocco\LaravelNotificationTemplate\Service\Command\CliMessage;
use GitBalocco\LaravelNotificationTemplate\Service\Command\ConfigureCheckService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class ConfigurationCheck extends Command
{
    /** @var string $signature */
    protected $signature = 'notification-template:config-check';
    /** @var string $description */
    protected $description = '設定ファイルの検証を行う';
    /** @var ConfigureCheckService $service */
    private $service;

    /**
     * @return int
     * @throws Exception
     */
    public function handle()
    {
        $this->init();

        $code = $this->preCheck();
        if ($code > 0) {
            $this->outputMessage();
            return $code;
        }

        $errorCount1 = $this->service->checkSettingsChannelLocaleCombination();
        $errorCount2 = $this->service->checkForAppLocale();
        $this->outputMessage();

        if ($errorCount1 + $errorCount2 > 0) {
            $this->line('============================');
            $this->line('Result:NG');
            $this->line(($errorCount1 + $errorCount2) . ' errors.');
            $this->line('Please correct the settings.');
            $this->line('============================');
            if ($errorCount1 > 0) {
                return 40;
            }
            if ($errorCount2 > 0) {
                return 50;
            }
        }


        $this->line('============================');
        $this->line('Result:OK');
        $this->line('No problem was detected.');
        $this->line('============================');

        return 0;
    }

    /**
     * @return void
     * TODO:初期化時に設定ファイルの有無を確認した方がよい・・・
     * ※現状、設定ファイルが存在しなくても、サービスクラスのインスタンス化時には例外が発生しない
     */
    private function init(): void
    {
        $this->service = App::make(ConfigureCheckService::class);
    }

    /**
     * @return int
     */
    private function preCheck()
    {
        if (!$this->service->checkLaravelCommonConfig()) {
            return 10;
        }
        if (!$this->service->checkMakeDataService()) {
            return 20;
        }
        if (!$this->service->canSettingsInstantiable()) {
            return 30;
        }
        return 0;
    }

    private function outputMessage()
    {
        /** @var CliMessage $cliMessage */
        foreach ($this->service->getMessages() as $cliMessage) {
            $statusName = $cliMessage->getStatus();
            $this->$statusName($cliMessage->getMessage());
        }
    }
}
