<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Service\Command;

use Exception;
use GitBalocco\LaravelNotificationTemplate\Common\LaravelCommonConfig;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;
use GitBalocco\LaravelNotificationTemplate\Repository\Contracts\NotificationSettingRepository;
use GitBalocco\LaravelNotificationTemplate\Service\Command\CliMessages;
use GitBalocco\LaravelNotificationTemplate\Service\Command\ConfigureCheckService;
use GitBalocco\LaravelNotificationTemplate\Service\NotificationSettingService;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Service\Command\ConfigureCheckService
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Service\Command\ConfigureCheckServiceTest
 */
class ConfigureCheckServiceTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ConfigureCheckService::class;

    /**
     * @covers ::makeLaravelCommonConfig
     */
    public function test_makeLaravelCommonConfig()
    {
        $stub = \Mockery::mock(LaravelCommonConfig::class);
        App::shouldReceive('make')->with(LaravelCommonConfig::class)->once()->andReturn($stub);
        App::shouldReceive('make')->with(NotificationSettingRepository::class);

        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();

        $targetClass->makeLaravelCommonConfig();
        return [$targetClass, $stub];
    }

    /**
     * @param mixed $depends
     * @covers ::laravelConfig
     * @depends test_makeLaravelCommonConfig
     */
    public function test_laravelConfig($depends)
    {
        $targetClass = $depends[0];

        //テスト対象メソッドの実行
        $actual = $targetClass->laravelConfig();
        //assertions
        $this->assertSame($depends[1], $actual);
    }

    /**
     * @covers ::laravelConfig
     */
    public function test_laravelConfig_IsNull()
    {
        App::shouldReceive('make')->with(NotificationSettingRepository::class);
        App::makePartial();
        $targetClass = new $this->testClassName();
        $this->expectException(Exception::class);
        //テスト対象メソッドの実行
        $targetClass->laravelConfig();
    }

    /**
     * @covers ::makeDataService
     */
    public function test_makeDataService()
    {
        App::shouldReceive('make')->with(NotificationSettingRepository::class);
        $stub = \Mockery::mock(NotificationSettingService::class);
        $stub->shouldReceive('repositoryClassName')->once()->withNoArgs();
        App::shouldReceive('make')->with(NotificationSettingService::class)->once()->andReturn($stub);
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->makeDataService();
        return [$targetClass, $stub];
    }

    /**
     * @param mixed $depends
     * @covers ::settings
     * @depends test_makeDataService
     */
    public function test_settings($depends)
    {
        $targetClass = $depends[0];
        $stub = $depends[1];
        $stub->shouldReceive('all')->once()->withNoArgs()->andReturn([]);
        //テスト対象メソッドの実行
        $actual = $targetClass->settings();
        $this->assertSame([], $actual);
    }

    /**
     * @covers ::settings
     */
    public function test_settings_IsNull()
    {
        App::shouldReceive('make')->with(NotificationSettingRepository::class);
        App::makePartial();
        $targetClass = new $this->testClassName();
        $this->expectException(Exception::class);
        //テスト対象メソッドの実行
        $targetClass->settings();
    }

    /**
     * @covers ::checkLaravelCommonConfig
     */
    public function test_checkLaravelCommonConfig()
    {
        //configのスタブ
        $stubConfig = \Mockery::mock(LaravelCommonConfig::class)->shouldIgnoreMissing();
        //対象クラスのモック
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('getMessages->add');
        $targetClass->shouldReceive('makeLaravelCommonConfig')->once()->andReturn($stubConfig);
        //テスト対象メソッド実行
        $actual = $targetClass->checkLaravelCommonConfig();
        $this->assertTrue($actual);
    }

    /**
     * @covers ::checkLaravelCommonConfig
     */
    public function test_checkLaravelCommonConfig_RaiseException()
    {
        //対象クラスのモック
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('getMessages->add');
        $targetClass->shouldReceive('makeLaravelCommonConfig')->once()->andThrowExceptions([new Exception()]);
        //テスト対象メソッド実行
        $actual = $targetClass->checkLaravelCommonConfig();
        $this->assertFalse($actual);
    }

    /**
     * @covers ::checkMakeDataService
     */
    public function test_checkMakeDataService()
    {
        //対象クラスのモック
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('getMessages->add');
        $targetClass->shouldReceive('makeDataService')->once()->andReturn('class_name');
        //テスト対象メソッド実行
        $actual = $targetClass->checkMakeDataService();
        $this->assertTrue($actual);
    }

    /**
     * @covers ::checkMakeDataService
     */
    public function test_checkMakeDataService_RaiseException()
    {
        //対象クラスのモック
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('getMessages->add');
        $targetClass->shouldReceive('makeDataService')->once()->andThrowExceptions([new Exception()]);
        //テスト対象メソッド実行
        $actual = $targetClass->checkMakeDataService();
        $this->assertFalse($actual);
    }

    /**
     * @covers ::canSettingsInstantiable
     */
    public function test_canSettingsInstantiable()
    {
        //対象クラスのモック
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('getMessages->add');
        $targetClass->shouldReceive('getRepository->all')->andReturn(
            [1 => ['id' => 1, 'array1'], 2 => ['id' => 2, 'array2']]
        );

        App::shouldReceive('make')->with(NotificationSetting::class, ['row' => ['id' => 1, 'array1']])->once();
        App::shouldReceive('make')->with(NotificationSetting::class, ['row' => ['id' => 2, 'array2']])->once();

        //テスト対象メソッド実行
        $actual = $targetClass->canSettingsInstantiable();
        $this->assertTrue($actual);
    }

    /**
     * @covers ::canSettingsInstantiable
     */
    public function test_canSettingsInstantiable_RaiseException()
    {
        //対象クラスのモック
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('getMessages->add');
        $targetClass->shouldReceive('getRepository->all')->andReturn(
            [1 => ['id' => 1, 'array1'], 2 => ['id' => 2, 'array2']]
        );

        App::shouldReceive('make')->with(NotificationSetting::class, ['row' => ['id' => 1, 'array1']])->once();
        App::shouldReceive('make')->with(NotificationSetting::class, ['row' => ['id' => 2, 'array2']])->once()
            ->andThrowExceptions([new Exception()]);

        //テスト対象メソッド実行
        $actual = $targetClass->canSettingsInstantiable();
        $this->assertFalse($actual);
    }

    /**
     * @covers ::checkSettingsChannelLocaleCombination
     */
    public function test_checkSettingsChannelLocaleCombination()
    {
        $stub = \Mockery::mock(NotificationSetting::class);
        //対象クラスのモック
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('getMessages->add');
        $targetClass->shouldReceive('settings')->andReturn([$stub, $stub]);
        $targetClass->shouldReceive('checkCombinations')->with($stub)->andReturn(1);

        //テスト対象メソッド実行
        $actual = $targetClass->checkSettingsChannelLocaleCombination();
        $this->assertSame(2, $actual);
    }

    /**
     * @covers ::checkCombinations
     */
    public function test_checkCombinations()
    {
        $stubArg = \Mockery::mock(NotificationSetting::class);
        $stubArg->shouldReceive('channels')->once()->andReturn(['mail', 'database']);
        $stubArg->shouldReceive('locales')->once()->andReturn(['ja', 'en']);

        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('tryGetViewName')->with($stubArg, 'mail', 'ja')->once();
        $targetClass->shouldReceive('tryGetViewName')->with($stubArg, 'database', 'ja')->once();
        $targetClass->shouldReceive('tryGetViewName')->with($stubArg, 'mail', 'en')->once();
        $targetClass->shouldReceive('tryGetViewName')->with($stubArg, 'database', 'en')
            ->once()->andReturn(1);

        //テスト対象メソッドの実行
        $actual = $targetClass->checkCombinations($stubArg);
        $this->assertSame(1, $actual);
    }

    /**
     * @covers ::tryGetViewName
     */
    public function test_tryGetViewName()
    {
        $stubArg = \Mockery::mock(NotificationSetting::class)->shouldIgnoreMissing();
        $stubViewName = \Mockery::mock(ViewName::class)->shouldIgnoreMissing();
        $stubArg->shouldReceive('getViewName')->once()->andReturn($stubViewName);
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('getMessages->add');
        $actual = $targetClass->tryGetViewName($stubArg, 'mail', 'ja');
        $this->assertSame(0, $actual);
    }

    /**
     * @covers ::tryGetViewName
     */
    public function test_tryGetViewName_RaiseException()
    {
        $stubArg = \Mockery::mock(NotificationSetting::class)->shouldIgnoreMissing();
        $stubArg->shouldReceive('getViewName')->once()->andThrowExceptions([new Exception()]);
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('getMessages->add');
        $actual = $targetClass->tryGetViewName($stubArg, 'mail', 'ja');
        $this->assertSame(1, $actual);
    }

    /**
     * @covers ::checkForAppLocale
     */
    public function test_checkForAppLocale()
    {
        $stub = \Mockery::mock(NotificationSetting::class);
        //対象クラスのモック
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('getMessages->add');
        $targetClass->shouldReceive('settings')->andReturn([$stub, $stub, $stub]);
        $targetClass->shouldReceive('checkCombinationsAppLocale')->with($stub)->andReturn(1);

        //テスト対象メソッド実行
        $actual = $targetClass->checkForAppLocale();
        $this->assertSame(3, $actual);
    }

    /**
     * @covers ::checkCombinationsAppLocale
     */
    public function test_checkCombinationsAppLocale()
    {
        $stubArg = \Mockery::mock(NotificationSetting::class);
        $stubArg->shouldReceive('channels')->once()->andReturn(['mail', 'database']);

        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('laravelConfig->getAppLocale')->andReturn('es');
        $targetClass->shouldReceive('tryGetViewName')->with($stubArg, 'mail', 'es')->once();
        $targetClass->shouldReceive('tryGetViewName')->with($stubArg, 'database', 'es')
            ->once()->andReturn(1);
        $targetClass->shouldReceive('getMessages->add');

        //テスト対象メソッドの実行
        $actual = $targetClass->checkCombinationsAppLocale($stubArg);
        $this->assertSame(1, $actual);
    }

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $stubMessage = \Mockery::mock(CliMessages::class);
        $stubRepo = \Mockery::mock(NotificationSettingRepository::class);

        App::shouldReceive('make')->with(CliMessages::class)->once()->andReturn($stubMessage);
        App::shouldReceive('make')->with(NotificationSettingRepository::class)->once()->andReturn($stubRepo);

        $targetClass = new $this->testClassName();
        return [$targetClass, $stubMessage, $stubRepo];
    }

    /**
     * @covers ::getRepository
     * @depends test___construct
     */
    public function test_getRepository($depends)
    {
        $targetClass = $depends[0];
        $stubRepo = $depends[2];

        \Closure::bind(
            function () use ($targetClass, $stubRepo) {
                //テスト対象メソッドの実行
                $actual = $targetClass->getRepository();
                //assertions
                $this->assertSame($stubRepo, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @covers ::getMessages
     * @depends test___construct
     */
    public function test_getMessages($depends)
    {
        $targetClass = $depends[0];
        $stubMessages = $depends[1];

        //テスト対象メソッドの実行
        $actual = $targetClass->getMessages();
        //assertions
        $this->assertSame($stubMessages, $actual);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

}
