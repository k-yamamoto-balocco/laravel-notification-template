<?php

namespace GitBalocco\LaravelNotificationTemplate\Common;

use GitBalocco\LaravelNotificationTemplate\Exceptions\BadConfigurationException;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use Illuminate\Support\Facades\App;

/**
 * 設定ファイルに必須項目が存在していることのチェックを行い、必須項目が存在していない場合に例外を発生させる。
 * このクラスでは、設定値の妥当性についての検証は実施しないこと。
 *
 * 値の妥当性については、Entity,ValueObjectをインスタンス化する際にチェックが行われるはずであり、このクラスの責務ではない。
 * また、 config:check コマンドの実行時により設定ファイルの妥当性を検証する手段は提供されている。
 *
 * Class ConfigurationChecker
 * @package GitBalocco\LaravelNotificationTemplate\Common
 */
class ConfigurationChecker
{

    /**
     * ConfigurationChecker constructor.
     */
    public function __construct(array $row)
    {
        $this->row = $row;
    }

    /**
     *
     */
    public function check()
    {
        $configuration = $this->row;

        if (!array_key_exists('id', $configuration)) {
            throw new BadConfigurationException('idが定義されていない。');
        }
        $id = $configuration['id'];

        if (!array_key_exists('name', $configuration)) {
            throw new BadConfigurationException('通知名が設定されていない');
        }

        if (!array_key_exists('notification_templates', $configuration)) {
            throw new BadConfigurationException('通知テンプレートが設定されていない');
        }

        if (!is_iterable($configuration['notification_templates'])) {
            throw new BadConfigurationException('テンプレート設定はiterableでなければならない。');
        }

        $count = 0;
        foreach ($configuration['notification_templates'] as $templatesSetting) {
            $this->checkTemplate($templatesSetting, $id);
            $count++;
        }

        if ($count === 0) {
            throw new BadConfigurationException('テンプレート設定が1件も定義されていない。');
        }
    }

    /**
     * @param array $templatesSetting
     * @param $notificationId
     */
    public function checkTemplate(array $templatesSetting, $notificationId)
    {
        if (!array_key_exists('id', $templatesSetting)) {
            throw new BadConfigurationException('通知' . $notificationId . ' テンプレートidが設定されていない項目が存在する');
        }

        if (!array_key_exists('channel', $templatesSetting)) {
            throw new BadConfigurationException(
                '通知' . $notificationId . ' テンプレート' . $templatesSetting['id'] . ' channelは必須'
            );
        }
        if (!array_key_exists('viewName', $templatesSetting)) {
            throw new BadConfigurationException(
                '通知' . $notificationId . ' テンプレート' . $templatesSetting['id'] . ' viewNameは必須'
            );
        }
        if (!array_key_exists('locale', $templatesSetting)) {
            throw new BadConfigurationException(
                '通知' . $notificationId . ' テンプレート' . $templatesSetting['id'] . ' localeは必須'
            );
        }

        //チャンネルごとのパラメータに今の所必須項目は存在しない。
        //mail の場合必須、などが今後必要になった場合、以下に追記が必要になる可能性もある。
    }
}
