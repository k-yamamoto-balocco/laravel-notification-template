<?php

namespace GitBalocco\LaravelNotificationTemplate\ValueObject;

use GitBalocco\KeyValueList\Contracts\BehaveAsKeyValueList;
use GitBalocco\KeyValueList\Contracts\Definer;
use GitBalocco\KeyValueList\Contracts\KeyValueListable;
use GitBalocco\KeyValueList\Definer\ArrayDefiner;
use GitBalocco\KeyValueList\LaravelCacheClassification;
use GitBalocco\KeyValueList\Traits\BehaveAsKeyValueListTrait;
use GitBalocco\LaravelNotificationTemplate\Drivers\Database\Contracts\DatabaseChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Drivers\Database\Database;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts\MailChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Mail;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\DefaultSetting;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\MailSetting;


/**
 * Class SupportedChannelList
 * @package GitBalocco\LaravelNotificationTemplate\Entity
 */
class SupportedChannelList extends LaravelCacheClassification implements BehaveAsKeyValueList
{
    use BehaveAsKeyValueListTrait;

    public function getDefiner(): Definer
    {
        return new ArrayDefiner(
            [
                [
                    'name' => 'mail',
                    'defaultDriver' => Mail::class,
                    'driverInterface' => MailChannelDriver::class,
                    'configClass' => MailSetting::class
                ],
                [
                    'name' => 'database',
                    'defaultDriver' => Database::class,
                    'driverInterface' => DatabaseChannelDriver::class,
                    'configClass' => DefaultSetting::class
                ],
            ]
        );
    }

    /**
     * @return KeyValueListable
     */
    public function representativeList(): KeyValueListable
    {
        return $this->nameList();
    }

    /**
     * @return KeyValueListable
     */
    public function nameList(): KeyValueListable
    {
        return $this->listOf('name');
    }

    /**
     * @param $identity
     * @return mixed
     */
    public function defaultDriverOf($identity)
    {
        return $this->valueOf('defaultDriver', $identity);
    }

    /**
     * @param $identity
     * @return mixed
     */
    public function driverInterfaceOf($identity)
    {
        return $this->valueOf('driverInterface', $identity);
    }

    /**
     * @param $identity
     * @return mixed
     */
    public function configClassOf($identity)
    {
        return $this->valueOf('configClass', $identity);
    }

    protected function getIdentityIndex()
    {
        return 'name';
    }

}