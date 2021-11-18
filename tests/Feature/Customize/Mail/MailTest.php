<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Feature\Customize\Mail;

use GitBalocco\LaravelNotificationTemplate\Common\LaravelCommonConfig;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\EmptyMailDriver;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Mail;
use GitBalocco\LaravelNotificationTemplate\Entity\EmptyDto;
use GitBalocco\LaravelNotificationTemplate\Exceptions\DtoMismatchException;
use GitBalocco\LaravelNotificationTemplate\Repository\Contracts\NotificationSettingRepository;
use GitBalocco\LaravelNotificationTemplate\TemplatedNotification;
use GitBalocco\LaravelNotificationTemplate\Test\TestCase;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\View\ViewFinderInterface;

/**
 * @coversNothing
 * メールドライバのカスタマイズを想定したテスト
 */
class CustomizeMailTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = Mail::class;

    public function test_mailDriverDto()
    {
        $this->init();
        $notification = new TemplatedNotification(1);
        $notification->assignToAll(new EmptyDto());
    }

    /**
     * パッケージ全体が実際に動作する状態となるよう設定を偽装する
     */
    public function init()
    {
        //File（設定ファイルが存在していることにする）
        File::shouldReceive('exists')->andReturnTrue();
        File::makePartial();
        //Config（設定ファイル内容をテスト用に準備）
        Config::set('app.locale', 'en');
        Config::set('mail.from.address', 'test-mail-driver@feature-test.example.com');
        Config::set('mail.from.name', 'テスト');
        Config::set('notification-template', $this->config());

        $config = new class() extends LaravelCommonConfig {
        };
        App::shouldReceive('make')->with(LaravelCommonConfig::class)->andReturn($config);

        //ViewFinder関連
        $mockFinder = \Mockery::mock(ViewFinderInterface::class);
        $mockFinder->shouldReceive('find')->andReturn('dummyPath');

        $mockFactory = \Mockery::mock(Factory::class);
        $mockFactory->shouldReceive('getFinder')->andReturn($mockFinder);

        App::shouldReceive('make')->with(Factory::class)
            ->andReturn(
                $mockFactory
            );

        //リポジトリ
        App::shouldReceive('make')->with(NotificationSettingRepository::class)
            ->andReturn(
                new \GitBalocco\LaravelNotificationTemplate\Repository\ConfigFile\NotificationSettingRepository()
            );

        App::makePartial();
    }

    public function config()
    {
        return [
            'notification_settings' => [
                1 => [
                    'id' => 1,
                    'name' => 'mail-driver-test1',
                    'notification_templates' => [
                        [
                            'id' => 1,
                            'viewName' => 'test',
                            'channel' => 'mail',
                            'dtoClass' => EmptyDto::class,
                            'locale' => 'ja',
                            'driver' => EmptyMailDriver::class,
                        ],
                    ],
                ],
                2 => [
                    'id' => 2,
                    'name' => 'mail-driver-test2',
                    'notification_templates' => [
                        [
                            'id' => 2,
                            'viewName' => 'test',
                            'channel' => 'mail',
                            'dtoClass' => EmptyDto::class,
                            'driver' => '',
                            'locale' => 'ja',

                        ],
                    ],
                ]
            ]
        ];
    }

    public function test_mailDriverDtoException()
    {
        $this->init();
        $notification = new TemplatedNotification(1);
        $this->expectException(DtoMismatchException::class);
        $notification->assignToAll(new \stdClass());
    }
}
