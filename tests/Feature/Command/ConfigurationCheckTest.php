<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Feature\Command;

use GitBalocco\LaravelNotificationTemplate\Command\ConfigurationCheck;
use GitBalocco\LaravelNotificationTemplate\Service\Command\CliMessages;
use GitBalocco\LaravelNotificationTemplate\Service\Command\ConfigureCheckService;
use Illuminate\Support\Facades\App;
use GitBalocco\LaravelNotificationTemplate\Test\TestCase;


/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Command\ConfigurationCheck
 * Tests\GitBalocco\LaravelNotificationTemplate\Command\ConfigurationCheckTest
 */
class ConfigurationCheckTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ConfigurationCheck::class;

    /**
     * @covers ::handle
     * @covers ::init
     * @covers ::preCheck
     * @covers ::outputMessage
     */
    public function test_handle_10(){
        $mock = \Mockery::mock(ConfigureCheckService::class)->shouldIgnoreMissing();
        $mock->shouldReceive('checkLaravelCommonConfig')->andReturn(false);
        $mock->shouldReceive('getMessages')->andReturn(new CliMessages());
        App::shouldReceive('make')->with(ConfigureCheckService::class)->andReturn($mock);
        App::partialMock();
        $this->artisan('notification-template:config-check')->assertExitCode(10);
    }

    /**
     * @covers ::handle
     * @covers ::init
     * @covers ::preCheck
     * @covers ::outputMessage
     */
    public function test_handle_20(){
        $mock = \Mockery::mock(ConfigureCheckService::class)->shouldIgnoreMissing();
        $mock->shouldReceive('getMessages')->andReturn(new CliMessages());

        $mock->shouldReceive('checkLaravelCommonConfig')->andReturn(true);
        $mock->shouldReceive('checkMakeDataService')->andReturn(false);

        App::shouldReceive('make')->with(ConfigureCheckService::class)->andReturn($mock);
        App::partialMock();
        $this->artisan('notification-template:config-check')->assertExitCode(20);
    }

    /**
     * @covers ::handle
     * @covers ::init
     * @covers ::preCheck
     * @covers ::outputMessage
     */
    public function test_handle_30(){
        $mock = \Mockery::mock(ConfigureCheckService::class)->shouldIgnoreMissing();
        $mock->shouldReceive('getMessages')->andReturn(new CliMessages());

        $mock->shouldReceive('checkLaravelCommonConfig')->andReturn(true);
        $mock->shouldReceive('checkMakeDataService')->andReturn(true);
        $mock->shouldReceive('canSettingsInstantiable')->andReturn(false);


        App::shouldReceive('make')->with(ConfigureCheckService::class)->andReturn($mock);
        App::partialMock();
        $this->artisan('notification-template:config-check')
            ->assertExitCode(30)
        ;
    }


    /**
     * @covers ::handle
     * @covers ::init
     * @covers ::preCheck
     * @covers ::outputMessage
     */
    public function test_handle_40(){
        $mock = \Mockery::mock(ConfigureCheckService::class)->shouldIgnoreMissing();
        $mock->shouldReceive('getMessages')->andReturn(new CliMessages());

        $mock->shouldReceive('checkLaravelCommonConfig')->andReturn(true);
        $mock->shouldReceive('checkMakeDataService')->andReturn(true);
        $mock->shouldReceive('canSettingsInstantiable')->andReturn(true);

        $mock->shouldReceive('checkSettingsChannelLocaleCombination')->andReturn(1);

        App::shouldReceive('make')->with(ConfigureCheckService::class)->andReturn($mock);
        App::partialMock();
        $this->artisan('notification-template:config-check')
            ->expectsOutput('Result:NG')
            ->assertExitCode(40);
    }


    /**
     * @covers ::handle
     * @covers ::init
     * @covers ::preCheck
     * @covers ::outputMessage
     */
    public function test_handle_50(){
        $mock = \Mockery::mock(ConfigureCheckService::class)->shouldIgnoreMissing();
        $mock->shouldReceive('getMessages')->andReturn(new CliMessages());

        $mock->shouldReceive('checkLaravelCommonConfig')->andReturn(true);
        $mock->shouldReceive('checkMakeDataService')->andReturn(true);
        $mock->shouldReceive('canSettingsInstantiable')->andReturn(true);
        $mock->shouldReceive('checkSettingsChannelLocaleCombination')->andReturn(0);
        $mock->shouldReceive('checkForAppLocale')->andReturn(1);

        App::shouldReceive('make')->with(ConfigureCheckService::class)->andReturn($mock);
        App::partialMock();
        $this->artisan('notification-template:config-check')
            ->expectsOutput('Result:NG')
            ->assertExitCode(50)
        ;
    }

    /**
     * @covers ::handle
     * @covers ::init
     * @covers ::preCheck
     * @covers ::outputMessage
     */
    public function test_handle_0(){
        $mock = \Mockery::mock(ConfigureCheckService::class)->shouldIgnoreMissing();
        $messages=new CliMessages();
        $messages->add('info','info-message');
        $messages->add('error','error-message');
        $mock->shouldReceive('getMessages')->andReturn($messages);

        $mock->shouldReceive('checkLaravelCommonConfig')->andReturn(true);
        $mock->shouldReceive('checkMakeDataService')->andReturn(true);
        $mock->shouldReceive('canSettingsInstantiable')->andReturn(true);
        $mock->shouldReceive('checkSettingsChannelLocaleCombination')->andReturn(0);
        $mock->shouldReceive('checkForAppLocale')->andReturn(0);

        App::shouldReceive('make')->with(ConfigureCheckService::class)->andReturn($mock);
        App::partialMock();

        $this->artisan('notification-template:config-check')
            ->expectsOutput('info-message')
            ->expectsOutput('error-message')
            ->expectsOutput('Result:OK')
            ->assertExitCode(0)
        ;
    }
}
