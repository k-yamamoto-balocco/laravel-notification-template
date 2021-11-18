<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Repository\ConfigFile;

use GitBalocco\LaravelNotificationTemplate\Common\LaravelCommonConfig;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;
use GitBalocco\LaravelNotificationTemplate\Exceptions\BadConfigurationException;
use GitBalocco\LaravelNotificationTemplate\Exceptions\ConfigFileNotFoundException;
use GitBalocco\LaravelNotificationTemplate\Repository\ConfigFile\NotificationSettingRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Repository\ConfigFile\NotificationSettingRepository
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Repository\ConfigFile\NotificationSettingRepositoryTest
 */
class NotificationSettingRepositoryTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = NotificationSettingRepository::class;

    /**
     * @covers ::__construct
     */
    public function test___construct_RaiseConfigFileNotFoundException()
    {
        $this->expectException(ConfigFileNotFoundException::class);
        File::shouldReceive('exists')->once()->andReturnFalse();
        new $this->testClassName();
    }

    /**
     * @covers ::__construct
     */
    public function test___construct_RaiseBadConfigurationException()
    {
        File::shouldReceive('exists')->once()->andReturnTrue();
        Config::set('notification-template.notification_settings', false);
        $this->expectException(BadConfigurationException::class);
        new $this->testClassName();
    }

    /**
     * @covers ::getById
     */
    public function test_getById()
    {
        //コンストラクタを通過するための準備
        File::shouldReceive('exists')->once()->andReturnTrue();
        Config::set('notification-template.notification_settings', true);

        //メソッド実行の準備

        $stubLaravelConfig = \Mockery::mock(LaravelCommonConfig::class)->shouldIgnoreMissing();
        Config::set('notification-template.notification_settings.999', ['any' => 'value']);
        App::shouldReceive('make')
            ->with(LaravelCommonConfig::class)
            ->once()
            ->andReturn($stubLaravelConfig);

        $targetClass = new $this->testClassName();
        //テスト対象メソッドの実行
        $actual = $targetClass->getById(999);
        $this->assertSame(['any' => 'value'], $actual);
    }

    /**
     * @covers ::all
     */
    public function test_all()
    {
        //コンストラクタを通過するための準備
        File::shouldReceive('exists')->once()->andReturnTrue();
        Config::set(
            'notification-template.notification_settings',
            [
                ['row1' => 'value1'],
                ['row2' => 'value2']
            ]
        );

        //メソッド実行の準備
        $stubResult = \Mockery::mock(NotificationSetting::class);
        $stubLaravelConfig = \Mockery::mock(LaravelCommonConfig::class)->shouldIgnoreMissing();

        App::shouldReceive('make')
            ->with(LaravelCommonConfig::class)
            ->once()
            ->andReturn($stubLaravelConfig);

        $targetClass = new $this->testClassName();
        //テスト対象メソッドの実行
        $actual = $targetClass->all();

        //assertions
        $this->assertIsIterable($actual);

        $array = iterator_to_array($actual);
        $this->assertSame(['row1' => 'value1'], $array[0]);
        $this->assertSame(['row2' => 'value2'], $array[1]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}
