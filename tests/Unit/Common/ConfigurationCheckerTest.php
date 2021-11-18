<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Common;

use GitBalocco\LaravelNotificationTemplate\Common\ConfigurationChecker;
use GitBalocco\LaravelNotificationTemplate\Exceptions\BadConfigurationException;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Common\ConfigurationChecker
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Common\ConfigurationCheckerTest
 */
class ConfigurationCheckerTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ConfigurationChecker::class;

    /**
     * @covers ::__construct
     * @dataProvider constructDataProviderRaiseException
     */
    public function test___construct_RaiseException($array)
    {
        $targetClass = new $this->testClassName($array);
        $this->assertInstanceOf(ConfigurationChecker::class, $targetClass);
        return $targetClass;
    }

    /**
     * @param mixed $array
     * @covers ::check
     * @dataProvider constructDataProviderRaiseException
     */
    public function test_check_RaiseException($array)
    {
        $targetClass = new $this->testClassName($array);
        $this->expectException(BadConfigurationException::class);
        $targetClass->check();
    }

    public function constructDataProviderRaiseException()
    {
        return [
            //TEST-CASE-01
            [
                //argument array is empty
                []
            ],
            //TEST-CASE-02
            [
                ['id' => 1234]
            ],
            //TEST-CASE-03
            [
                ['id' => 1234, 'name' => 'notification_name']
            ],
            //TEST-CASE-04
            [
                ['id' => 1234, 'name' => 'notification_name', 'notification_templates' => 'not_iterable_value']
            ],
            //TEST-CASE-05
            [
                ['id' => 1234, 'name' => 'notification_name', 'notification_templates' => []]
            ],
        ];
    }

    /**
     * @covers ::check
     */
    public function test_check()
    {
        $array = [
            'id' => 5678,
            'name' => 'notification_name',
            'notification_templates' => [
                ['dummy_template_setting1'],
                ['dummy_template_setting2'],
                ['dummy_template_setting3'],
            ]
        ];
        $targetClass = \Mockery::mock($this->testClassName, [$array])->makePartial();
        $targetClass->shouldReceive('checkTemplate')->with(['dummy_template_setting1'], 5678)->once();
        $targetClass->shouldReceive('checkTemplate')->with(['dummy_template_setting2'], 5678)->once();
        $targetClass->shouldReceive('checkTemplate')->with(['dummy_template_setting3'], 5678)->once();
        $targetClass->check();
    }


    /**
     * @covers ::checkTemplate
     * @dataProvider checkTemplateRaiseExceptionDataProvider
     */
    public function test_checkTemplate_RaiseException($array)
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        $this->expectException(BadConfigurationException::class);
        $targetClass->checkTemplate($array, 111222);
    }

    public function checkTemplateRaiseExceptionDataProvider()
    {
        return [
            //TEST-CASE-01
            [
                //empty array
                []
            ],
            //TEST-CASE-02
            [
                //only id
                ['id' => 9012]
            ],
            //TEST-CASE-03
            [
                //id,channel
                ['id' => 9012, 'channel' => 'mail']
            ],
            //TEST-CASE-04
            [
                //id,channel
                ['id' => 9012, 'channel' => 'mail', 'viewName' => 'view.name']
            ],


        ];
    }
}
