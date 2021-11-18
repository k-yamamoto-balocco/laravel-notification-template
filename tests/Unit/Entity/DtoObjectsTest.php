<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\Entity;

use GitBalocco\LaravelNotificationTemplate\Common\ArrayDirectProduct;
use GitBalocco\LaravelNotificationTemplate\Entity\Contracts\NotificationTemplate;
use GitBalocco\LaravelNotificationTemplate\Entity\DtoObjects;
use GitBalocco\LaravelNotificationTemplate\Entity\NotificationSetting;
use GitBalocco\LaravelNotificationTemplate\Exceptions\DtoMismatchException;
use GitBalocco\LaravelNotificationTemplate\Repository\Contracts\NotificationSettingRepository;
use GitBalocco\LaravelNotificationTemplate\Service\NotificationSettingService;
use GitBalocco\LaravelNotificationTemplate\ValueObject\ClassName;
use GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel;
use GitBalocco\LaravelNotificationTemplate\ValueObject\SupportedChannelList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\Entity\DtoObjects
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\Entity\DtoObjectsTest
 */
class DtoObjectsTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = DtoObjects::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $stubNotificationSetting = \Mockery::mock(NotificationSetting::class);
        $targetClass = $this->createTargetClass($stubNotificationSetting);

        $this->assertInstanceOf(DtoObjects::class, $targetClass);
    }

    public function createTargetClass($stubNotificationSetting, $stubCollection = null)
    {
        $stubService = \Mockery::mock(NotificationSettingService::class);

        $stubService->shouldReceive('getById')
            ->with(999)
            ->once()
            ->andReturn($stubNotificationSetting);

        App::shouldReceive('make')
            ->with(NotificationSettingService::class)
            ->once()
            ->andReturn($stubService);

        App::shouldReceive('make')
            ->with(Collection::class)
            ->once()
            ->andReturn($stubCollection ?? collect());

        $targetClass = \Mockery::mock($this->testClassName, [999])->makePartial()->shouldAllowMockingProtectedMethods();

        return $targetClass;
    }

    /**
     * @covers ::setDtoObjectToChannel
     */
    public function test_setDtoObjectToChannel()
    {
        //テスト対象クラスのMock作成の準備
        $stubNotificationSetting = \Mockery::mock(NotificationSetting::class);

        $stubTemplates = $this->stubTemplates();
        $stubNotificationSetting->shouldReceive('getTemplates')->once()->andReturn($stubTemplates);

        //テスト対象作成
        $targetClass = $this->createTargetClass($stubNotificationSetting);
        //equals() が常にtrueを返すように制御するので、setDtoObject()はテンプレートの数だけ呼び出されることになる。
        $targetClass->shouldReceive('setDtoObject')->times(count($stubTemplates));

        //メソッド引数の準備
        $stubChannel = \Mockery::mock(NotificationChannel::class);
        //equals() は、ループ回数呼び出される。
        $stubChannel->shouldReceive('equals')->times(count($stubTemplates))->andReturnTrue();

        $dtoObject = new \stdClass();

        //テスト対象メソッドの実行
        $actual = $targetClass->setDtoObjectToChannel($stubChannel, $dtoObject);
        $this->assertInstanceOf(DtoObjects::class, $actual);
    }

    public function stubTemplates()
    {
        $channelList = new SupportedChannelList();

        //locale 3種類 × 対応channel の全組み合わせを用意する。
        $combination = new ArrayDirectProduct(['ja', 'en', 'es'], $channelList->all());
        $stubTemplates = [];
        foreach ($combination as $item) {
            $stubNotificationTemplate = \Mockery::mock(NotificationTemplate::class);
            $stubNotificationTemplate->shouldReceive('getLocale')->andReturn($item[0]);
            $stubNotificationTemplate->shouldReceive('getChannel')->andReturn(new NotificationChannel($item[1]));
            $stubTemplates[] = $stubNotificationTemplate;
        }
        return $stubTemplates;
    }

    /**
     * @covers ::setDtoObjectToLocale
     */
    public function test_setDtoObjectToLocale()
    {
        //テスト対象クラスのMock作成の準備
        $stubNotificationSetting = \Mockery::mock(NotificationSetting::class);

        $stubTemplates = $this->stubTemplates();
        $stubNotificationSetting->shouldReceive('getTemplates')->once()->andReturn($stubTemplates);

        //テスト対象作成
        $targetClass = $this->createTargetClass($stubNotificationSetting);
        //テンプレートはLocale3種×対応チャンネル数　の組み合わせ用意されるので、localeが一致する回数は、対応チャンネル数と必ず一致する
        $channelList = new SupportedChannelList();
        $targetClass->shouldReceive('setDtoObject')->times(count($channelList->all()));


        //テスト対象メソッドの実行 locale:ja
        $actual = $targetClass->setDtoObjectToLocale('ja', new \stdClass());
        $this->assertInstanceOf(DtoObjects::class, $actual);
    }

    /**
     * @covers ::setDtoObjectToAll
     */
    public function test_setDtoObjectToAll()
    {
        //テスト対象クラスのMock作成の準備
        $stubNotificationSetting = \Mockery::mock(NotificationSetting::class);

        $stubTemplates = $this->stubTemplates();
        $stubNotificationSetting->shouldReceive('getTemplates')->once()->andReturn($stubTemplates);

        //テスト対象作成
        $targetClass = $this->createTargetClass($stubNotificationSetting);
        //全パターンに対してセットするので、テンプレート数と同数呼び出されるはず
        $targetClass->shouldReceive('setDtoObject')->times(count($stubTemplates));

        //テスト対象メソッドの実行
        $actual = $targetClass->setDtoObjectToAll(new \stdClass());
        $this->assertInstanceOf(DtoObjects::class, $actual);
    }

    /**
     * @covers ::getDtoObject
     * @covers ::toKey
     */
    public function test_getDtoObject()
    {
        //テスト対象クラスのMock作成の準備
        $stubNotificationSetting = \Mockery::mock(NotificationSetting::class);

        $stubCollection = \Mockery::mock(Collection::class);
        $stubCollection->shouldReceive('get')->andReturn('resultOtoObject');

        //テスト対象作成
        $targetClass = $this->createTargetClass($stubNotificationSetting, $stubCollection);

        //引数作成
        $stubChannel = \Mockery::mock(NotificationChannel::class);
        $stubChannel->shouldReceive('__toString')->andReturn('mail');

        $locale = 'ja';

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $stubChannel, $locale) {
                $actual = $targetClass->getDtoObject($stubChannel, $locale);
                //assertions
                $this->assertSame('resultOtoObject', $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @covers ::setDtoObject
     * @covers ::getSetting
     */
    public function test_setDtoObject_raiseException()
    {
        //テスト対象クラスのMock作成の準備
        $stubNotificationSetting = \Mockery::mock(NotificationSetting::class);

        //型検査に失敗するよう、無名クラスのクラス名を返すよう仕込んでおく
        $stubNotificationSetting->shouldReceive('getDtoClass')
            ->once()
            ->andReturn(
                new ClassName(
                    get_class(
                        new class() {
                        }
                    )
                )
            );
        //テスト対象作成
        $targetClass = $this->createTargetClass($stubNotificationSetting);

        //引数作成
        $stubChannel = \Mockery::mock(NotificationChannel::class);
        $locale = 'ja';
        //$dtoObjectをstdClassのオブジェクトにする。
        //このオブジェクトの型と、無名クラスの型をis_aで検査するので、結果がfalseとなり例外が発生する。
        $dtoObject = new \stdClass();

        //実行により例外が発生するはずである
        $this->expectException(DtoMismatchException::class);
        $targetClass->setDtoObject($stubChannel, $locale, $dtoObject);
    }

    /**
     * @covers ::setDtoObject
     */
    public function test_setDtoObject()
    {
        //テスト対象クラスのMock作成の準備
        $clonedDtoObject = new \stdClass();
        $clonedDtoObject->thisIsClonedObject = 'YES YES YES';

        $stubNotificationSetting = \Mockery::mock(NotificationSetting::class);
        $stubCollection = \Mockery::mock(Collection::class);

        //collectionに保存されるのは、クローンされたオブジェクトであるはず。
        $stubCollection->shouldReceive('put')->once();


        //型検証に成功するよう、stdClassのクラス名を返す。
        $stubNotificationSetting->shouldReceive('getDtoClass')
            ->once()
            ->andReturn(new ClassName(\stdClass::class));

        //テスト対象作成
        $targetClass = $this->createTargetClass($stubNotificationSetting, $stubCollection);

        //メソッド引数の用意 チャンネル : database
        $stubChannel = \Mockery::mock(NotificationChannel::class);
        $stubChannel->shouldReceive('__toString')->andReturn('database');
        //メソッド引数の用意 locale : es
        $locale = 'es';

        //メソッド引数の用意 dtoObject cloneされた際の挙動を制御するために、__clone()を追加した無名クラスを作る。
        //型検証に成功するよう、\stdClassを継承。
        $dtoObject = new class($clonedDtoObject) extends \stdClass {

            public function __construct($clonedDtoObject)
            {
                $this->cloned = $clonedDtoObject;
            }

            public function __clone()
            {
                return $this->cloned;
            }
        };

        $actual = $targetClass->setDtoObject($stubChannel, $locale, $dtoObject);
        $this->assertInstanceOf(DtoObjects::class, $actual);
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
