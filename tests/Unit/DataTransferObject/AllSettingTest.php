<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\DataTransferObject;

use GitBalocco\LaravelNotificationTemplate\DataTransferObject\AllSetting;
use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\DataTransferObject\AllSetting
 * Tests\GitBalocco\LaravelNotificationTemplate\DataTransferObject\AllSettingTest
 */
class AllSettingTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = AllSetting::class;

    /**
     * @covers ::toArray
     * @covers ::__construct
     */
    public function test_toArray()
    {
        $stubTemplate = \Mockery::mock(NotificationTemplate::class)->makePartial();
        $stubTemplate->shouldReceive('getId')->once();
        $stubTemplate->shouldReceive('getChannel')->once();
        $stubTemplate->shouldReceive('getLocale')->once();
        $stubTemplate->shouldReceive('getViewName')->once();
        $stubTemplate->shouldReceive('getDtoClass')->once();
        $stubTemplate->shouldReceive('getDriver')->once();

        $stubTemplates = [
            $stubTemplate
        ];

        $stubItem = \Mockery::mock(NotificationSetting::class)->makePartial();
        $stubItem->shouldReceive('getId')->once();
        $stubItem->shouldReceive('getName')->once();
        $stubItem->shouldReceive('getTemplates')->once()->andReturn($stubTemplates);
        $argArray = [
            $stubItem
        ];

        $targetClass = new $this->testClassName($argArray);
        //テスト対象メソッドの実行
        $targetClass->toArray();
    }

    /**
     * @covers ::header
     */
    public function test_header()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        //テスト対象メソッドの実行
        $actual = $targetClass->header();
        //assertions
        $this->assertIsArray($actual);
    }
}
