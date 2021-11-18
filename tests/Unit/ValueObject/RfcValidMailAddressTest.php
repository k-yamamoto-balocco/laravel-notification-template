<?php

namespace GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use GitBalocco\LaravelNotificationTemplate\ValueObject\RfcValidMailAddress;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;
use Swift_RfcComplianceException;

/**
 * @coversDefaultClass \GitBalocco\LaravelNotificationTemplate\ValueObject\RfcValidMailAddress
 * GitBalocco\LaravelNotificationTemplate\Tests\Unit\ValueObject\RfcValidMailAddressTest
 */
class RfcValidMailAddressTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = RfcValidMailAddress::class;

    /**
     * @covers ::setValue
     * @covers ::__construct
     */
    public function test_setValue_RaiseExceptionArgument()
    {
        $this->expectException(Swift_RfcComplianceException::class);
        new $this->testClassName('string_doesnt_match_rfc_validator');
    }

    /**
     * @covers ::setValue
     * @covers ::__construct
     */
    public function test_setValue_ValidArgument()
    {
        $targetClass = new $this->testClassName('nice@example.com');
        $this->assertSame('nice@example.com', $targetClass->getValue());
    }

    /**
     * @covers ::setValue
     */
    public function test_setValue_RaiseExceptionLogic()
    {
        $stubValidator = new class() {
            public function isValid()
            {
                return false;
            }
        };

        App::shouldReceive('make')
            ->with(EmailValidator::class)
            ->once()
            ->andReturn($stubValidator);

        App::shouldReceive('make')->with(RFCValidation::class);

        $this->expectException(Swift_RfcComplianceException::class);
        new $this->testClassName('any_string_value');
    }

    /**
     * @covers ::setValue
     */
    public function test_setValue_Logic()
    {
        $stubValidator = new class() {
            public function isValid()
            {
                return true;
            }
        };

        App::shouldReceive('make')
            ->with(EmailValidator::class)
            ->once()
            ->andReturn($stubValidator);

        App::shouldReceive('make')->with(RFCValidation::class);
        $targetClass = new $this->testClassName('any_string_value');

        $this->assertSame('any_string_value', $targetClass->getValue());
    }

    /**
     * @coversNothing
     */
    public function test_serialize()
    {
        $targetClass = new $this->testClassName('nice@example.com');
        try {
            serialize($targetClass);
            //OK
            $this->assertTrue(true);
        } catch (\Throwable $e) {
            //必ず失敗するAssertion
            $this->assertTrue(false, $e->getMessage());
        }
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }
}
