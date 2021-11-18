<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Common;

use GitBalocco\LaravelNotificationTemplate\Common\LaravelCommonConfig;
use GitBalocco\LaravelNotificationTemplate\ValueObject\RfcValidMailAddress;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Common\LaravelCommonConfig
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Common\LaravelCommonConfigTest
 */
class LaravelCommonConfigTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = LaravelCommonConfig::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        Config::set('app.locale', 'app_locale_value');
        Config::set('mail.from.address', 'mail_from_value@rfcvalid.example.com');
        Config::set('mail.from.name', 'mail_from_value');
        $targetClass = new $this->testClassName();
        //このassertは特に意味が無い
        $this->assertInstanceOf(LaravelCommonConfig::class, $targetClass);

        return $targetClass;
    }

    /**
     * @param mixed $targetClass
     * @covers ::getAppLocale
     * @depends test___construct
     */
    public function test_getAppLocale($targetClass)
    {
        $actual = $targetClass->getAppLocale();
        $this->assertIsString($actual);
        $this->assertSame('app_locale_value', $actual);
    }

    /**
     * @param mixed $targetClass
     * @covers ::getMailFromAddress
     * @depends test___construct
     */
    public function test_getMailFromAddress($targetClass)
    {
        $actual = $targetClass->getMailFromAddress();
        $this->assertInstanceOf(RfcValidMailAddress::class, $actual);
        $this->assertSame('mail_from_value@rfcvalid.example.com', (string)$actual);
    }

    /**
     * @param mixed $targetClass
     * @covers ::getMailFromName
     * @depends test___construct
     */
    public function test_getMailFromName($targetClass){
        $actual = $targetClass->getMailFromName();
        $this->assertIsString($actual);
        $this->assertSame('mail_from_value', $actual);
    }
}
