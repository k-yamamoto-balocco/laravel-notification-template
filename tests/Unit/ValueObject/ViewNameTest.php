<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject;

use GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\View\ViewFinderInterface;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\ValueObject\ViewName
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject\ViewNameTest
 */
class ViewNameTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ViewName::class;

    /**
     * @covers ::setValue
     * @covers ::__construct
     * @covers ::getPath
     * @covers ::find
     */
    public function test_setValue()
    {
        $stubViewFinder = \Mockery::mock(ViewFinderInterface::class);
        $stubViewFinder->shouldReceive('find')->andReturn('string_view_path');
        $stubFactory = \Mockery::mock(Factory::class);
        $stubFactory->shouldReceive('getFinder')->once()->andReturn($stubViewFinder);
        App::shouldReceive('make')->with(Factory::class)->once()->andReturn($stubFactory);

        $targetClass = new $this->testClassName('string_view_name');

        $this->assertSame('string_view_name', $targetClass->getValue());
        $this->assertSame('string_view_path', $targetClass->getPath());
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

}
