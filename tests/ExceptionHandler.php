<?php

namespace KodePandai\ApiResponse\Tests;

use KodePandai\ApiResponse\ApiExceptionHandler;
use Orchestra\Testbench\Exceptions\Handler;
use Throwable;

class ExceptionHandler extends Handler
{
    public function render($request, Throwable $e)
    {
        return ApiExceptionHandler::render($e, $request);
    }
}
