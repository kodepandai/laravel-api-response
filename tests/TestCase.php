<?php

namespace KodePandai\ApiResponse\Tests;

use KodePandai\ApiResponse\ApiResponseServiceProvider;

/**
 * @see https://packages.tools/testbench/basic/testcase.html
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [ ApiResponseServiceProvider::class ];
    }
}
