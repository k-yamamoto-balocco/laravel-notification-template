<?php

namespace GitBalocco\LaravelNotificationTemplate\ValueObject;

use GitBalocco\LaravelNotificationTemplate\Common\LaravelCommonConfig;
use Illuminate\Support\Facades\App;

class MailFrom
{
    /** @var RfcValidMailAddress $address */
    private $address;
    /** @var string|null $name */
    private $name;

    /**
     * MailFrom constructor.
     * @param string|null $address
     * @param string|null $name
     */
    public function __construct(string $address = null, string $name = null)
    {
        //引数が与えられていない場合、Laravel設定を参照してインスタンス化
        if (is_null($address)) {
            /** @var LaravelCommonConfig $commonConfig */
            $commonConfig = App::make(LaravelCommonConfig::class);
            $this->address = $commonConfig->getMailFromAddress();
        } else {
            $this->address = App::make(RfcValidMailAddress::class, ['value' => $address]);
        }

        $this->name = $name;
    }

    /**
     * @return RfcValidMailAddress
     */
    public function getAddress(): RfcValidMailAddress
    {
        return $this->address;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
