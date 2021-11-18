<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Drivers\Mail;

use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts\HasFrom;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts\HasSubject;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts\MailChannelDriver;
use GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Mail;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate\MailSetting as MailConfig;
use GitBalocco\LaravelNotificationTemplate\ValueObject\MailFrom;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\AnonymousNotifiable;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Mail
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Drivers\Mail\MailTest
 */
class MailTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = Mail::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $config = \Mockery::mock(MailConfig::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $notifiable = \Mockery::mock(AnonymousNotifiable::class)->shouldIgnoreMissing();

        $targetClass = \Mockery::mock($this->testClassName, [$config, new \stdClass(), $notifiable])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $this->assertInstanceOf(MailChannelDriver::class, $targetClass);
        $this->assertInstanceOf(Mailable::class, $targetClass);
    }

    /**
     * @covers ::build
     */
    public function test_build()
    {
        //スタブの準備
        $stubConfig = \Mockery::mock(MailConfig::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $stubNotifiable = \Mockery::mock(AnonymousNotifiable::class)->shouldIgnoreMissing();
        $stubNotifiable->shouldReceive('routeNotificationFor')->andReturn('notify@example.com');

        $stubViewName = \Mockery::mock(ViewName::class);
        $stubViewName->shouldReceive('__toString')->once()->andReturn('string_view_name');
        $stubMailFrom = \Mockery::mock(MailFrom::class);
        $stubMailFrom->shouldReceive('getAddress->getValue')
            ->once()
            ->andReturn('string_decided_from_address');
        $stubMailFrom->shouldReceive('getName')
            ->once()
            ->andReturn('string_decided_from_name');

        $dtoObject = new \stdClass();

        //テスト対象クラスのモック作成
        $targetClass = \Mockery::mock($this->testClassName, [$stubConfig, new \stdClass(),$stubNotifiable])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        //クラス内メソッド呼び出しの結果を変更
        $targetClass->shouldReceive('getConfig->getViewName')
            ->once()
            ->andReturn($stubViewName);

        $targetClass->shouldReceive('getDtoObject')
            ->once()
            ->andReturn($dtoObject);

        $targetClass->shouldReceive('getNotifiable')
            ->once()
            ->andReturn($stubNotifiable);

        $targetClass->shouldReceive('text')->with('string_view_name', ['dto' => $dtoObject])->once();
        $targetClass->shouldReceive('decideSubject')->once()->andReturn('string_decided_subject');
        $targetClass->shouldReceive('subject')->with('string_decided_subject')->once();
        $targetClass->shouldReceive('decideFrom')->once()->andReturn($stubMailFrom);
        $targetClass->shouldReceive('from')
            ->with('string_decided_from_address', 'string_decided_from_name')
            ->once();

        $actual = $targetClass->build();
        $this->assertInstanceOf(Mailable::class, $actual);

    }

    /**
     * @covers ::decideSubject
     */
    public function test_decideSubject_dtoHasSubject()
    {
        //スタブの準備
        $stubConfig = \Mockery::mock(MailConfig::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $stubNotifiable = \Mockery::mock(AnonymousNotifiable::class)->shouldIgnoreMissing();

        //テスト対象クラスのモック作成
        $targetClass = \Mockery::mock($this->testClassName, [$stubConfig, new \stdClass(),$stubNotifiable])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $hasSubjectDto = \Mockery::mock(\stdClass::class, HasSubject::class)->shouldAllowMockingProtectedMethods();
        $hasSubjectDto->shouldReceive('subject')->andReturn('string_subject');

        $targetClass->shouldReceive('getDtoObject')->twice()->andReturn($hasSubjectDto);

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass) {
                $actual = $targetClass->decideSubject();
                //assertions
                $this->assertSame('string_subject', $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @covers ::decideSubject
     */
    public function test_decideSubject_configContainsSubject()
    {
        //スタブの準備
        $stubConfig = \Mockery::mock(MailConfig::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $stubConfig->shouldReceive('getSubject')->once()->andReturn('subject_in_config');

        $stubNotifiable = \Mockery::mock(AnonymousNotifiable::class)->shouldIgnoreMissing();

        //テスト対象クラスのモック作成
        $targetClass = \Mockery::mock($this->testClassName, [$stubConfig, new \stdClass(),$stubNotifiable])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        //HasSubjectをimplementしていないオブジェクト
        $targetClass->shouldReceive('getDtoObject')->once()->andReturn(new \stdClass());


        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass) {
                $actual = $targetClass->decideSubject();
                //assertions
                $this->assertSame('subject_in_config', $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @covers ::decideSubject
     */
    public function test_decideSubject_notDetected()
    {
        //スタブの準備
        $stubConfig = \Mockery::mock(MailConfig::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $stubConfig->shouldReceive('getSubject')->once()->andReturn();

        $stubNotifiable = \Mockery::mock(AnonymousNotifiable::class)->shouldIgnoreMissing();

        //テスト対象クラスのモック作成
        $targetClass = \Mockery::mock($this->testClassName, [$stubConfig, new \stdClass(),$stubNotifiable])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        //HasSubjectをimplementしていないオブジェクト
        $targetClass->shouldReceive('getDtoObject')->once()->andReturn(new \stdClass());

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass) {
                $actual = $targetClass->decideSubject();
                //assertions
                $this->assertSame('', $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }


    /**
     * @covers ::decideFrom
     */
    public function test_decideFrom_stoHasFrom()
    {
        //スタブの準備
        $stubConfig = \Mockery::mock(MailConfig::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $stubMailFrom = \Mockery::mock(MailFrom::class);

        $stubNotifiable = \Mockery::mock(AnonymousNotifiable::class)->shouldIgnoreMissing();

        //テスト対象クラスのモック作成
        $targetClass = \Mockery::mock($this->testClassName, [$stubConfig, new \stdClass(),$stubNotifiable])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $dtoHasFromMethod = \Mockery::mock(\stdClass::class, HasFrom::class)->shouldAllowMockingProtectedMethods();
        $dtoHasFromMethod->shouldReceive('from')->andReturn($stubMailFrom);

        $targetClass->shouldReceive('getDtoObject')->twice()->andReturn($dtoHasFromMethod);

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $stubMailFrom) {
                $actual = $targetClass->decideFrom();
                //assertions
                $this->assertInstanceOf(MailFrom::class, $actual);
                $this->assertSame($stubMailFrom, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @covers ::decideFrom
     */
    public function test_decideFrom()
    {
        //スタブの準備
        $stubMailFrom = \Mockery::mock(MailFrom::class);

        $stubConfig = \Mockery::mock(MailConfig::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $stubConfig->shouldReceive('getFrom')->once()->andReturn($stubMailFrom);

        $stubNotifiable = \Mockery::mock(AnonymousNotifiable::class)->shouldIgnoreMissing();

        //テスト対象クラスのモック作成
        $targetClass = \Mockery::mock($this->testClassName, [$stubConfig, new \stdClass(),$stubNotifiable])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $dtoDontHaveFromMethod = new \stdClass();
        $targetClass->shouldReceive('getDtoObject')->once()->andReturn($dtoDontHaveFromMethod);


        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $stubMailFrom) {
                $actual = $targetClass->decideFrom();
                //assertions
                $this->assertInstanceOf(MailFrom::class, $actual);
                $this->assertSame($stubMailFrom, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    protected function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
        \Mockery::close();
    }


}
