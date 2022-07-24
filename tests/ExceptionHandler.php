<?php

namespace KodePandai\ApiResponse\Tests;

use KodePandai\ApiResponse\ExceptionHandler as ApiExceptionHandler;
use Orchestra\Testbench\Exceptions\Handler;
use Throwable;

class ExceptionHandler extends Handler
{
    public function render($request, Throwable $e)
    {
        return ApiExceptionHandler::renderAsApiResponse($e);
    }
}
