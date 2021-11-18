<?php

namespace GitBalocco\LaravelNotificationTemplate\Test\Unit\Drivers\Database;

use GitBalocco\LaravelNotificationTemplate\Drivers\Database\Contracts\DatabaseChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Drivers\Database\Database;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\DefaultSetting as DatabaseConfig;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName;
use Illuminate\Support\Facades\View;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Drivers\Database\Database
 * GitBalocco\LaravelNotificationTemplate\Test\Unit\Drivers\Database\DatabaseTest
 */
class DatabaseTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = Database::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $config = \Mockery::mock(DatabaseConfig::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $notifiable = new \stdClass();
        $targetClass = \Mockery::mock($this->testClassName, [$config, new \stdClass(),$notifiable])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $this->assertInstanceOf(DatabaseChannelDriver::class, $targetClass);
    }

    /**
     * @covers ::build
     */
    public function test_build()
    {
        //スタブの準備
        $stubConfig = \Mockery::mock(DatabaseConfig::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $stubView = \Mockery::mock(\stdClass::class, \Illuminate\Contracts\View\View::class);
        $stubView->shouldReceive('render')->once()->andReturn('rendered view');

        $stubViewName = \Mockery::mock(ViewName::class);
        $dtoObject = new \stdClass();

        //テスト対象クラスのモック作成
        $notifiable = new \stdClass();
        $targetClass = \Mockery::mock($this->testClassName, [$stubConfig, new \stdClass(),$notifiable])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        //クラス内メソッド呼び出しの結果を変更
        $targetClass->shouldReceive('getConfig->getViewName')
            ->once()
            ->andReturn($stubViewName);

        $targetClass->shouldReceive('getDtoObject')
            ->once()
            ->andReturn($dtoObject);

        //Viewファサードのモック
        View::shouldReceive('make')
            ->with($stubViewName, ['dto' => $dtoObject])
            ->once()
            ->andReturn($stubView);


        //テスト対象メソッドの実行
        $actual = $targetClass->build();

        //assertions
        $this->assertSame(['message' => 'rendered view'], $actual);
    }
}
