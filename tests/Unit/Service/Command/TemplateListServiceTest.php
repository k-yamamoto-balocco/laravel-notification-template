<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Service\Command;

use GitBalocco\LaravelNotificationTemplate\DataTransferObject\AllSetting;
use GitBalocco\LaravelNotificationTemplate\Service\Command\TemplateListService;
use GitBalocco\LaravelNotificationTemplate\Service\NotificationSettingService;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Service\Command\TemplateListService
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Service\Command\TemplateListServiceTest
 */
class TemplateListServiceTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = TemplateListService::class;

    /**
     * @covers ::__construct
     * @author kenji yamamoto <k.yamamoto@balocco.info>
     */
    public function test___construct()
    {
        $stubService = \Mockery::mock(NotificationSettingService::class)->shouldIgnoreMissing();
        $stubAllSetting = \Mockery::mock(AllSetting::class)->shouldIgnoreMissing();
        $this->createObject($stubService, $stubAllSetting);
    }

    public function createObject($stubService, $stubAllSetting)
    {
        App::shouldReceive('make')
            ->with(NotificationSettingService::class)
            ->once()
            ->andReturn($stubService);

        App::shouldReceive('make')
            ->with(AllSetting::class, ['settings' => null])
            ->once()
            ->andReturn($stubAllSetting);
        return new $this->testClassName();
    }

    /**
     * @covers ::getDto
     */
    public function test_getDto()
    {
        $stubService = \Mockery::mock(NotificationSettingService::class)->shouldIgnoreMissing();
        $stubAllSetting = \Mockery::mock(AllSetting::class)->shouldIgnoreMissing();
        $targetClass = $this->createObject($stubService, $stubAllSetting);
        $actual = $targetClass->getDto();
        $this->assertSame($stubAllSetting, $actual);
    }

}
