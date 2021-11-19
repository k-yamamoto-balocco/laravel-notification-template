<?php

namespace GitBalocco\LaravelNotificationTemplate\ValueObject;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Support\Facades\App;
use Swift_RfcComplianceException;

/**
 * Class ValidMailAddress
 * @package GitBalocco\LaravelNotificationTemplate\ValueObject
 */
class RfcValidMailAddress extends StringValue
{
    /** @var EmailValidator $validator */
    private $validator;

    /**
     * RfcValidMailAddress constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->validator = App::make(EmailValidator::class);
        parent::__construct($value);
    }

    /**
     * @param string $value
     * @throws Swift_RfcComplianceException
     */
    protected function setValue(string $value): void
    {
        if (!$this->validator->isValid($value, App::make(RFCValidation::class))) {
            throw new Swift_RfcComplianceException('Invalid mail address.');
        }
        $this->value = $value;
    }
}
