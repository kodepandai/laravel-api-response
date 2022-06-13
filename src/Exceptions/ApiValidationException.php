<?php

namespace KodePandai\ApiResponse\Exceptions;

use Illuminate\Http\Response;
use KodePandai\ApiResponse\ApiResponse;

class ApiValidationException extends ApiException
{
    /**
     * Throw a validation error api exception.
     *
     * @param array $errors The errors
     */
    public function __construct(array $errors = [])
    {
        parent::__construct(
            'The given data was invalid.',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $errors
        );
    }

    /**
     * Get the response.
     */
    public function getResponse(): ApiResponse
    {
        return ApiResponse::error($this->errors)
        ->title('Validation Error')
        ->message($this->getMessage())
        ->statusCode($this->getStatusCode());
    }
}
