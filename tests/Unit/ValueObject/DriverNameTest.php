<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject;

use GitBalocco\LaravelNotificationTemplate\Drivers\Database\Contracts\DatabaseChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts\MailChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Mail;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ClassName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DriverName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\ValueObject\DriverName
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject\DriverNameTest
 */
class DriverNameTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = DriverName::class;

    /**
     * @coversNothing
     */
    public function test___construct()
    {
        $stubChannel = \Mockery::mock(NotificationChannel::class);
        //検査にパスするよう、コンストラクタ引数で渡すオブジェクトと同じクラス名を返すように設定
        $stubChannel->shouldReceive('driverInterfaceName')->andReturn(\stdClass::class);
        $targetClass = new $this->testClassName($stubChannel, \stdClass::class);
        $this->assertInstanceOf(ClassName::class, $targetClass);
    }

    /**
     * @covers ::__construct
     * @covers ::setValue
     */
    public function test_setValue_Default()
    {
        $stubChannel = \Mockery::mock(NotificationChannel::class);
        $stubChannel->shouldReceive('defaultDriverClassName')->once()->andReturn(Mail::class);
        //実際に利用される際と同じ動作となるように、Mailが実装するべきインターフェース名としてMailChannelDriverを返す
        $stubChannel->shouldReceive('driverInterfaceName')->once()->andReturn(MailChannelDriver::class);
        $targetClass = new $this->testClassName($stubChannel, '');
        $this->assertSame(Mail::class, $targetClass->getValue());
    }

    /**
     * @covers ::setValue
     */
    public function test_setValue_RaiseException()
    {
        $stubChannel = \Mockery::mock(NotificationChannel::class);
        $stubChannel->shouldReceive('defaultDriverClassName')->once()->andReturn(Mail::class);
        //例外が発生するように、インターフェース名としてDatabaseChannelDriverを返す
        //Mailはこのインターフェースを実装していないため、例外が発生する。
        $stubChannel->shouldReceive('driverInterfaceName')->once()->andReturn(DatabaseChannelDriver::class);
        $this->expectException(InvalidArgumentException::class);
        new $this->testClassName($stubChannel, '');
    }

    /**
     * @coversNothing
     */
    public function test_serialize()
    {
        $channel = new NotificationChannel('mail');
        $targetClass = new DriverName($channel, Mail::class);
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
