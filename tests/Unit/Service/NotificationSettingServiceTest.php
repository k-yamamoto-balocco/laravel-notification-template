<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Service;

use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;
use GitBalocco\LaravelNotificationTemplate\Repository\Contracts\NotificationSettingRepository;
use GitBalocco\LaravelNotificationTemplate\Service\NotificationSettingService;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Service\NotificationSettingService
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Service\NotificationSettingServiceTest
 */
class NotificationSettingServiceTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = NotificationSettingService::class;

    /**
     * @covers ::__construct
     * @author kenji yamamoto <k.yamamoto@balocco.info>
     */
    public function test___construct()
    {
        $stubRepo = \Mockery::mock(NotificationSettingRepository::class);
        $targetClass = $this->createObject($stubRepo);

        \Closure::bind(
            function () use ($targetClass, $stubRepo) {
                //assertions
                $this->assertSame($stubRepo, $targetClass->repository);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @param $stubRepo
     * @return mixed
     */
    public function createObject($stubRepo)
    {
        App::shouldReceive('make')->with(NotificationSettingRepository::class)->once()->andReturn($stubRepo);
        return new $this->testClassName();
    }

    /**
     * @covers ::all
     */
    public function test_all()
    {
        App::shouldReceive('make')->with(NotificationSetting::class, ['row' => ['array1']])->once();
        App::shouldReceive('make')->with(NotificationSetting::class, ['row' => ['array2']])->once();

        $stubRepo = \Mockery::mock(NotificationSettingRepository::class);
        $stubRepo->shouldReceive('all')->withNoArgs()->andReturn([0 => ['array1'],1=>['array2']]);
        $targetClass = $this->createObject($stubRepo);

        //テスト対象メソッドの実行
        $actual = $targetClass->all();
        //イテレータを変換しないと実行されないので・・・
        iterator_to_array($actual);
    }

    /**
     * @covers ::getById
     */
    public function test_getById()
    {
        $stubResult = \Mockery::mock(NotificationSetting::class);
        App::shouldReceive('make')->with(NotificationSetting::class, ['row' => [0 => 'array']])->once()->andReturn(
            $stubResult
        );
        $stubRepo = \Mockery::mock(NotificationSettingRepository::class);
        $stubRepo->shouldReceive('getById')->with(12345)->once()->andReturn([0 => 'array']);
        $targetClass = $this->createObject($stubRepo);

        //テスト対象メソッドの実行
        $actual = $targetClass->getById(12345);
        $this->assertSame($stubResult, $actual);
    }

    /**
     * @covers ::repositoryClassName
     */
    public function test_repositoryClassName()
    {
        $stubRepo = \Mockery::mock(NotificationSettingRepository::class);
        $targetClass = $this->createObject($stubRepo);

        //テスト対象メソッドの実行
        $actual = $targetClass->repositoryClassName();
        $this->assertSame(get_class($stubRepo), $actual);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}
