<?php

namespace GitBalocco\LaravelNotificationTemplate\Drivers\Mail;

use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts\HasFrom;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts\HasSubject;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts\MailChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Drivers\Traits\ChannelDriverTrait;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\MailSetting;
use GitBalocco\LaravelNotificationTemplate\ValueObject\MailFrom;
use Illuminate\Mail\Mailable;

/**
 * Class TemplateMailable
 * このクラスはMailableをextendしなければならない。
 * また、コンストラクタをTraitに回すのはイマイチ感があるため、コンストラクタを独自に定義している。
 * Mailに関する機能を追加する場合、このクラスと、Dtoクラスに機能追加をすれば良い。
 * @package GitBalocco\LaravelNotificationTemplate\Drivers\Mail
 * @method MailSetting getConfig() : NotificationTemplate
 */
class Mail extends Mailable implements MailChannelDriver
{
    use ChannelDriverTrait;

    /**
     * Mail constructor.
     * @param MailSetting $config
     * @param $dtoObject
     * @param $notifiable
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
        $viewName = $this->getConfig()->getViewName();
        $this->text((string)$viewName, ['dto' => $this->getDtoObject()]);
        $this->subject($this->decideSubject());
        $from = $this->decideFrom();
        $this->from($from->getAddress()->getValue(), $from->getName());
        $this->to($this->getNotifiable()->routeNotificationFor('mail'));
        return $this;
    }

    /**
     * @return string
     */
    protected function decideSubject(): string
    {
        //dtoにsubject() メソッドがあれば、その結果を返す
        if (is_a($this->getDtoObject(), HasSubject::class)) {
            if ($subject = $this->getDtoObject()->subject()) {
                return (string)$subject;
            }
        }
        //configに指定されていれば、その結果を返す
        if ($subject = $this->getConfig()->getSubject()) {
            return (string)$subject;
        }
        //いずれも無ければ空文字列を返す
        return '';
    }

    /**
     * @return MailFrom
     */
    protected function decideFrom(): MailFrom
    {
        //dtoにfrom() メソッドがあれば、その結果を返す
        if (is_a($this->getDtoObject(), HasFrom::class)) {
            return $this->getDtoObject()->from();
        }

        //configに指定されていれば、その結果を返す
        return $this->getConfig()->getFrom();
    }
}