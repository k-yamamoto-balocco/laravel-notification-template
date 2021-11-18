<?php


namespace GitBalocco\LaravelNotificationTemplate\ValueObject;

use stdClass;

class DtoClassName extends ClassName
{
    public function setValue(string $class): void
    {
        if ($class === '') {
            $class = stdClass::class;
        }
        parent::setValue($class);
    }

}