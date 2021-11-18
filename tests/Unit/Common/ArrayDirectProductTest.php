<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Common;

use GitBalocco\LaravelNotificationTemplate\Common\ArrayDirectProduct;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Common\ArrayDirectProduct
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Common\ArrayDirectProductTest
 */
class ArrayDirectProductTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ArrayDirectProduct::class;

    /**
     * @covers ::__construct
     * @dataProvider constructorDataProvider
     * @param array $expected
     * @param mixed ...$items
     * @return mixed
     */
    public function test___construct(array $expected, ...$items)
    {
        $targetClass = new $this->testClassName(...$items);

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $expected) {
                //assertions of constructor
                $this->assertSame($expected, $targetClass->arrays);
            },
            $this,
            $targetClass
        )->__invoke();

        return $targetClass;
    }

    /**
     * @covers ::exec
     * @dataProvider execDataProvider
     */
    public function test_exec($expected, ...$arrays)
    {
        $targetClass = \Mockery::mock($this->testClassName, [$arrays])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $expected, $arrays) {
                $traversable = $targetClass->exec(...$arrays);
                foreach ($traversable as $key => $actual) {
                    $this->assertSame($expected[$key], $actual);
                }
            },
            $this,
            $targetClass
        )->__invoke();
    }

    public function execDataProvider()
    {
        return [
            //testcase1
            [
                //expects
                [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']],
                //array1
                [1, 2],
                //array2
                ['a', 'b']
            ],
            //testcase2
            [
                //expects
                [[10, 'X'], [10, 'Y'], [10, 'Z']],
                //array1
                [10],
                //array2
                ['X', 'Y', 'Z']
            ],
            //testcase3
            [
                //expects
                [[true], [false]],
                //array1
                [true, false],
            ],
            //testcase3
            [
                //expects
                [
                    [1, 4, 7],
                    [1, 4, 8],
                    [1, 4, 9],
                    [1, 5, 7],
                    [1, 5, 8],
                    [1, 5, 9],
                    [1, 6, 7],
                    [1, 6, 8],
                    [1, 6, 9],
                    [2, 4, 7],
                    [2, 4, 8],
                    [2, 4, 9],
                    [2, 5, 7],
                    [2, 5, 8],
                    [2, 5, 9],
                    [2, 6, 7],
                    [2, 6, 8],
                    [2, 6, 9],
                    [3, 4, 7],
                    [3, 4, 8],
                    [3, 4, 9],
                    [3, 5, 7],
                    [3, 5, 8],
                    [3, 5, 9],
                    [3, 6, 7],
                    [3, 6, 8],
                    [3, 6, 9]
                ],
                //array1
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 9],
            ],

        ];
    }

    public function constructorDataProvider()
    {
        return [
            //testcase1
            [
                //expected
                [[]],
                //array1
                []
            ],
            //testcase2
            [
                //expected
                [[1, 2], ['a', 'b']],
                //array1
                [1, 2],
                //array2
                ['a', 'b']
            ],

        ];
    }

    /**
     * @dataProvider constructorDataProvider
     * @covers ::getIterator
     * @param array $expected
     * @param mixed ...$items
     */
    public function test_getIterator(array $expected, ...$items)
    {
        $stubTraversable = \Mockery::mock(\Traversable::class);
        $mock = \Mockery::mock($this->testClassName, [$items])->shouldAllowMockingProtectedMethods()->makePartial();
        $mock->shouldReceive('exec')->once()->with($expected)->andReturn($stubTraversable);
        $actual = $mock->getIterator();
        $this->assertSame($stubTraversable, $actual);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}
