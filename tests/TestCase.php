<?php

namespace KodePandai\ApiResponse\Tests;

use KodePandai\ApiResponse\ApiResponseServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * @see https://packages.tools/testbench/basic/testcase.html
 */
class TestCase extends BaseTestCase
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [ApiResponseServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \KodePandai\ApiResponse\Tests\TestExceptionHandler::class,
        );
    }
}
