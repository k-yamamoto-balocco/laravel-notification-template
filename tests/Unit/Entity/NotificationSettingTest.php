<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Entity;

use GitBalocco\LaravelNotificationTemplate\Common\ConfigurationChecker;
use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;
use GitBalocco\LaravelNotificationTemplate\Exceptions\TemplateNotFoundException;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DriverName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\DtoClassName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationSettingName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Entity\NotificationSettingTest
 */
class NotificationSettingTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = NotificationSetting::class;

    /**
     * @covers ::via
     */
    public function test_via()
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('channels')->once();
        $targetClass->via();
    }

    /**
     * @covers ::channels
     * @dataProvider channelsDataProvider
     */
    public function test_channels($rows, $expects)
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getTemplates')->once()->andReturn($rows);
        $actual = $targetClass->channels();
        $this->assertSame($expects, $actual);
    }

    /**
     * @covers ::getDtoClass
     */
    public function test_getDtoClass()
    {
        $stubChannel = \Mockery::mock(NotificationChannel::class);
        $locale = 'ja';

        $stubDtoClassName = \Mockery::mock(DtoClassName::class);

        $stubTemplate = \Mockery::mock(NotificationTemplate::class);
        $stubTemplate->shouldReceive('getDtoClass')->once()->andReturn($stubDtoClassName);

        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getTemplate')->with($stubChannel, $locale)->once()->andReturn($stubTemplate);

        $actual = $targetClass->getDtoClass($stubChannel, $locale);
        $this->assertSame($stubDtoClassName, $actual);
    }

    /**
     * @covers ::getViewName
     */
    public function test_getViewName()
    {
        $stubChannel = \Mockery::mock(NotificationChannel::class);
        $locale = 'ja';

        $stubViewName = \Mockery::mock(ViewName::class);

        $stubTemplate = \Mockery::mock(NotificationTemplate::class);
        $stubTemplate->shouldReceive('getViewName')->once()->andReturn($stubViewName);

        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getTemplate')->with($stubChannel, $locale)->once()->andReturn($stubTemplate);

        //テスト対象メソッドの実行
        $actual = $targetClass->getViewName($stubChannel, $locale);
        //assertions
        $this->assertSame($stubViewName, $actual);
    }

    /**
     * @covers ::getDriverName
     */
    public function test_getDriverName()
    {
        $stubChannel = \Mockery::mock(NotificationChannel::class);
        $locale = 'ja';

        $stubDriverName = \Mockery::mock(DriverName::class);

        $stubTemplate = \Mockery::mock(NotificationTemplate::class);
        $stubTemplate->shouldReceive('getDriver')->once()->andReturn($stubDriverName);

        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getTemplate')->with($stubChannel, $locale)->once()->andReturn($stubTemplate);

        //テスト対象メソッドの実行
        $actual = $targetClass->getDriverName($stubChannel, $locale);
        //assertions
        $this->assertSame($stubDriverName, $actual);
    }

    /**
     * @covers ::getTemplate
     */
    public function test_getTemplate()
    {
        $argChannel = new NotificationChannel('mail');
        $argLocale = 'ja';

        $stubArray = [];
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getTemplates')->once()->andReturn($stubArray);

        $stubResult = \Mockery::mock(NotificationTemplate::class)->shouldAllowMockingProtectedMethods();

        $collectionStub = \Mockery::mock(Collection::class);
        $collectionStub->shouldReceive('where')->with('channel', '=', 'mail')->once()->andReturnSelf();
        $collectionStub->shouldReceive('where')->with('locale', '=', 'ja')->once()->andReturnSelf();
        $collectionStub->shouldReceive('first')->once()->andReturn($stubResult);

        App::shouldReceive('make')
            ->with(Collection::class, ['items' => $stubArray])
            ->once()
            ->andReturn($collectionStub);

        App::makePartial();

        $targetClass->getTemplate($argChannel, $argLocale);
    }

    /**
     *
     */
    public function test_getTemplate_RaiseException()
    {
        $argChannel = new NotificationChannel('mail');
        $argLocale = 'ja';

        $stubArray = [];
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getTemplates')->once()->andReturn($stubArray);


        $collectionStub = \Mockery::mock(Collection::class);
        $collectionStub->shouldReceive('where')->with('channel', '=', 'mail')->once()->andReturnSelf();
        $collectionStub->shouldReceive('where')->with('locale', '=', 'ja')->once()->andReturnSelf();
        $collectionStub->shouldReceive('first')->once()->andReturnNull();

        App::shouldReceive('make')
            ->with(Collection::class, ['items' => $stubArray])
            ->once()
            ->andReturn($collectionStub);

        App::makePartial();

        $this->expectException(TemplateNotFoundException::class);
        $targetClass->getTemplate($argChannel, $argLocale);
    }


    public function getTemplateDataProvider()
    {
        return [
            //TEST-CASE-01
            [
                //templates dummy
                [
                    ['no' => 1, 'channel' => 'mail', 'locale' => 'ja'],
                    ['no' => 2, 'channel' => 'mail', 'locale' => 'en'],
                    ['no' => 3, 'channel' => 'database', 'locale' => 'ja'],
                    ['no' => 4, 'channel' => 'database', 'locale' => 'en'],
                ],
                //channel-string
                'database',
                //locale
                'ja',
                //expected
                3
            ],
            //TEST-CASE-02
            [],
        ];
    }

    /**
     * @return array[]
     */
    public function channelsDataProvider()
    {
        return [
            //TEST-CASE-01
            [
                [
                    ['channel' => 'mail'],
                    ['channel' => 'database'],
                    ['channel' => 'mail'],
                ],
                //重複が省かれる
                ['mail', 'database']
            ],
            //TEST-CASE-02
            [
                [
                    ['channel' => 'a'],
                    ['other_key' => 'b'],
                    ['other_key' => 'mail'],
                ],
                //channel 以外のキーは無視される
                ['a']
            ],
            //TEST-CASE-03
            [
                [],
                //要素がなければ空配列
                []
            ],

        ];
    }

    /**
     * @covers ::locales
     * @dataProvider localesDataProvider
     */
    public function test_locales($rows, $expects)
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getTemplates')->once()->andReturn($rows);
        $actual = $targetClass->locales();
        $this->assertSame($expects, $actual);
    }

    public function localesDataProvider()
    {
        return [
            //TEST-CASE-01
            [
                [
                    ['locale' => 'ja'],
                    ['locale' => 'en'],
                    ['locale' => 'ja'],
                    ['locale' => 'es'],
                ],
                //重複が省かれる
                ['ja', 'en', 'es']
            ],
            //TEST-CASE-02
            [
                [
                    ['locale' => 'a'],
                    ['locale' => 'b'],
                    ['other_key' => 'mail'],
                ],
                //locale 以外のキーは無視される
                ['a', 'b']
            ],
            //TEST-CASE-03
            [
                [],
                //要素がなければ空配列
                []
            ],
        ];
    }

    /**
     * @covers ::__construct
     * @author kenji yamamoto <k.yamamoto@balocco.info>
     */
    public function test___construct()
    {
        $argArray = [
            'id' => 999,
            'name' => 'notification_name',
            'notification_templates' => [
                ['channel' => 'dummy_channel'],
                ['channel' => 'dummy_channel'],
            ]
        ];

        $stubChecker = \Mockery::mock(ConfigurationChecker::class);
        $stubChecker->shouldReceive('check')->once();

        $stubNotificationSettingName = \Mockery::mock(NotificationSettingName::class);

        $stubNotificationTemplate = \Mockery::mock(NotificationTemplate::class);

        $stubNotificationChannel = \Mockery::mock(NotificationChannel::class);
        $stubNotificationChannel->shouldReceive('notificationTemplateObject')
            ->andReturn($stubNotificationTemplate);

        App::shouldReceive('make')
            ->with(ConfigurationChecker::class, ['row' => $argArray])
            ->once()
            ->andReturn($stubChecker);

        App::shouldReceive('make')
            ->with(NotificationSettingName::class, ['value' => $argArray['name']])
            ->once()
            ->andReturn($stubNotificationSettingName);

        App::shouldReceive('make')
            ->with(NotificationChannel::class, ['value' => 'dummy_channel'])
            ->twice()
            ->andReturn($stubNotificationChannel);

        $targetClass = new $this->testClassName($argArray);
        return [$targetClass, $stubNotificationSettingName, $stubNotificationTemplate];
    }

    /**
     * @covers ::getId
     * @depends test___construct
     * @param $depends
     */
    public function test_getId($depends)
    {
        $targetClass = $depends[0];
        //テスト対象メソッドの実行
        $actual = $targetClass->getId();
        $this->assertSame(999, $actual);
    }

    /**
     * @param mixed $depends
     * @covers ::getName
     * @depends test___construct
     */
    public function test_getName($depends)
    {
        $targetClass = $depends[0];
        $notificationName = $depends[1];
        //テスト対象メソッドの実行
        $actual = $targetClass->getName();
        //assertions
        $this->assertSame($notificationName, $actual);
    }

    /**
     * @param mixed $depends
     * @covers ::getTemplates
     * @depends test___construct
     */
    public function test_getTemplates($depends)
    {
        $targetClass = $depends[0];
        $stubNotificationTemplate = $depends[2];

        //テスト対象メソッドの実行
        $actual = $targetClass->getTemplates();
        //assertions
        $this->assertIsArray($actual);
        foreach ($actual as $item) {
            $this->assertSame($stubNotificationTemplate, $item);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}
