<?php


namespace GitBalocco\LaravelNotificationTemplate\Drivers\Mail;

use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts\MailChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Drivers\Traits\ChannelDriverTrait;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\MailSetting;
use Illuminate\Mail\Mailable;

/**
 * Class EmptyMailDriver
 * @package GitBalocco\LaravelNotificationTemplate\Drivers\Mail
 * @codeCoverageIgnore
 */
class EmptyMailDriver extends Mailable implements MailChannelDriver
{
    use ChannelDriverTrait;

    /**
     * CustomMailDriver constructor.
     * @param MailSetting $config
     * @param mixed $dtoObject
     * @param mixed $notifiable
     */
    public function __construct(MailSetting $config, $dtoObject, $notifiable)
    {
        $this->config = $config;
        $this->dtoObject = $dtoObject;
        $this->notifiable = $notifiable;
    }

    /**
     * @return $this
     */
    public function build(): Mailable
    {
        //カスタマイズ処理を実装

        //mail チャンネルの場合、Mailableを返却する。※このパッケージは MailMessage には対応していません
        return $this;
    }
}
