<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Entity\NotificationTemplate;

use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\DefaultSetting;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\MailSetting;
use GitBalocco\LaravelNotificationTemplate\ValueObject\MailFrom;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\MailSetting
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Entity\NotificationTemplate\MailSettingTest
 */
class MailSettingTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = MailSetting::class;

    /**
     * @covers ::__construct
     * @author kenji yamamoto <k.yamamoto@balocco.info>
     */
    public function test___construct()
    {
        //Arg
        $viewName = 'arg_view_name';

        $channel = 'mail';//これは本物を作るので、対応しているチャンネル名
        $locale = 'arg_locale';
        $dtoClass = \stdClass::class;//これは本物を作るので、存在するクラス名
        $driver = '';
        $mailFrom = ['arg' => 'mail_from'];
        $subject = 'arg_subject';

        $stubMailFrom = \Mockery::mock(MailFrom::class);
        $stubViewName = \Mockery::mock(ViewName::class);

        App::shouldReceive('make')->with(MailFrom::class, $mailFrom)->once()->andReturn($stubMailFrom);
        //view nameだけは本物を使えないので、親クラスコンストラクタの処理を差し替えに行く
        App::shouldReceive('make')->with(ViewName::class, ['value' => $viewName])->andReturn($stubViewName);
        App::makePartial();


        $targetClass = new $this->testClassName(
            999,
            $viewName,
            $channel,
            $locale,
            $dtoClass,
            $driver,
            $mailFrom,
            $subject
        );
        $this->assertInstanceOf(DefaultSetting::class, $targetClass);
        $this->assertInstanceOf(NotificationTemplate::class, $targetClass);

        return [$targetClass, $stubMailFrom];
    }

    /**
     * @param mixed $depends
     * @covers ::getSubject
     * @depends test___construct
     */
    public function test_getSubject($depends)
    {
        $targetClass = $depends[0];
        //テスト対象メソッドの実行
        $actual = $targetClass->getSubject();
        //assertions
        $this->assertSame('arg_subject', $actual);
    }

    /**
     * @param mixed $depends
     * @covers ::getFrom
     * @depends test___construct
     */
    public function test_getFrom($depends)
    {
        $targetClass = $depends[0];
        $mailFrom = $depends[1];
        //テスト対象メソッドの実行
        $actual = $targetClass->getFrom();
        //assertions
        $this->assertSame($mailFrom, $actual);
    }

}
