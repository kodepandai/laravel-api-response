<?php

namespace KodePandai\ApiResponse\Exceptions;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Validation\ValidationException;
use KodePandai\ApiResponse\ApiResponse;

class ApiValidationException extends ValidationException implements Responsable
{
    public function __construct($validator)
    {
        $response = ApiResponse::unprocessable()
            ->errors($validator->errors()->toArray());

        parent::__construct($validator, $response);
    }

    public function toResponse($request)
    {
        return $this->getResponse();
    }

    public static function invalid(string $key, string $message = null): static
    {
        $message = $message ?: __(':key is invalid.', ['key' => $key]);

        return static::withMessages([$key => [$message]]);
    }
}
