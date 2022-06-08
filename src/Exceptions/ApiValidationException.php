<?php

namespace KodePandai\ApiResponse\Exceptions;

use Illuminate\Http\Response;
use InvalidArgumentException;
use KodePandai\ApiResponse\ApiResponse;

class ApiValidationException extends ApiException
{
    public function __construct(public array $errors = [])
    {
        $valuesNotArray = array_filter($errors, fn ($key) => ! is_array($key));

        if (count($valuesNotArray) > 0) {
            throw new InvalidArgumentException('Invalid $errors format');
        }
    }

    public function toResponse($request)
    {
        return ApiResponse::error($this->errors)
            ->title('Validation Error')
            ->message('The given data was invalid.')
            ->status(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->toResponse($request);
    }
}
