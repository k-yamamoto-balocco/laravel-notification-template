<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Feature;

use GitBalocco\LaravelNotificationTemplate\Exceptions\ConfigFileNotFoundException;
use GitBalocco\LaravelNotificationTemplate\Repository\Contracts\NotificationSettingRepository;
use GitBalocco\LaravelNotificationTemplate\ServiceProvider;
use GitBalocco\LaravelNotificationTemplate\Test\TestCase;
use Illuminate\Support\Facades\App;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\ServiceProvider
 * GitBalocco\LaravelNotificationTemplate\Tests\Feature\ServiceProviderTest
 */
class ServiceProviderTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ServiceProvider::class;

    /**
     * @covers ::register
     * @covers ::boot
     */
    public function test_()
    {
        $this->expectException(ConfigFileNotFoundException::class);
        $this->artisan('notification-template:config-check')->assertExitCode(20);
        $this->artisan('notification-template:list')->assertExitCode(1);
    }

}
