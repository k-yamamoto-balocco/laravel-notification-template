<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject;

use GitBalocco\LaravelNotificationTemplate\Common\LaravelCommonConfig;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DriverName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\MailFrom;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use GitBalocco\LaravelNotificationTemplate\ValueObject\RfcValidMailAddress;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\ValueObject\MailFrom
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject\MailFromTest
 */
class MailFromTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = MailFrom::class;

    /**
     * @covers ::__construct
     */
    public function test___construct_Default()
    {
        $stubValidMail = \Mockery::mock(RfcValidMailAddress::class);

        $stubLaravelConfig = \Mockery::mock(LaravelCommonConfig::class);
        $stubLaravelConfig->shouldReceive('getMailFromAddress')->once()->andReturn($stubValidMail);
        App::shouldReceive('make')->with(LaravelCommonConfig::class)->once()->andReturn($stubLaravelConfig);
        new $this->testClassName(null, null);
    }

    /**
     * @covers ::__construct
     */
    public function test___construct_WithAddress()
    {
        $stubValidMail = \Mockery::mock(RfcValidMailAddress::class);
        App::shouldReceive('make')
            ->with(RfcValidMailAddress::class, ['value' => 'test@example.com'])
            ->once()
            ->andReturn($stubValidMail);

        return [
            new $this->testClassName('test@example.com', 'string_from_name'),
            $stubValidMail
        ];
    }

    /**
     * @covers ::getAddress
     * @param $depends
     * @depends test___construct_WithAddress
     */
    public function test_getAddress($depends)
    {
        $targetClass = $depends[0];
        $stubValidMail = $depends[1];
        //テスト対象メソッドの実行
        $actual = $targetClass->getAddress();
        //assertions
        $this->assertInstanceOf(RfcValidMailAddress::class, $actual);
        $this->assertSame($stubValidMail, $actual);
    }

    /**
     * @param $depends
     * @covers ::getName
     * @depends test___construct_WithAddress
     */
    public function test_getName($depends)
    {
        $targetClass = $depends[0];

        //テスト対象メソッドの実行
        $actual = $targetClass->getName();
        //assertions
        $this->assertSame('string_from_name', $actual);
    }

    /**
     * @coversNothing
     */
    public function test_serialize()
    {
        $targetClass = new $this->testClassName(null, null);
        try {
            serialize($targetClass);
            //OK
            $this->assertTrue(true);
        } catch (\Throwable $e) {
            //必ず失敗するAssertion
            $this->assertTrue(false, $e->getMessage());
        }
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

}
