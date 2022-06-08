<?php

namespace KodePandai\ApiResponse\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use KodePandai\ApiResponse\ApiResponse;

class ApiException extends Exception implements Responsable
{
    public function toResponse($request)
    {
        return ApiResponse::error()
            ->status($this->getCode() ?: Response::HTTP_BAD_REQUEST)
            ->message($this->getMessage())
            ->toResponse(($request));
    }
}
