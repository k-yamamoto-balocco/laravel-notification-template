<?php

namespace GitBalocco\LaravelNotificationTemplate\Test;

/**
 * Class TestCase
 * @package GitBalocco\LaravelNotificationTemplate\Test
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [\GitBalocco\LaravelNotificationTemplate\ServiceProvider::class];
    }
}
