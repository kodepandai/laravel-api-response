<?php

namespace KodePandai\ApiResponse\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use KodePandai\ApiResponse\ApiResponse;

class ApiException extends Exception implements Responsable
{
    protected ApiResponse $response;

    public function __construct(string $message = '', string $title = '', int $statusCode = 500)
    {
        $this->response = match ($statusCode) {
            ApiResponse::HTTP_NOT_FOUND => ApiResponse::notFound(),
            ApiResponse::HTTP_UNPROCESSABLE_ENTITY => ApiResponse::unprocessable(),
            ApiResponse::HTTP_UNAUTHORIZED => ApiResponse::unauthorized(),
            ApiResponse::HTTP_FORBIDDEN => ApiResponse::forbidden(),
            ApiResponse::HTTP_BAD_REQUEST => ApiResponse::badRequest(),
            default /* HTTP_INTERNAL_SERVER_ERROR */ => ApiResponse::error(),
        };

        if (! empty($title)) {
            $this->response->title($title);
        }

        if (! empty($message)) {
            $this->response->message($message);
        }
    }

    public function toResponse($request)
    {
        return $this->response;
    }

    public function title(string $title): static
    {
        $this->response->title($title);

        return $this;
    }

    public function withTitle(string $title): static
    {
        return $this->title($title);
    }

    public function message(string $message): static
    {
        $this->response->message($message);

        return $this;
    }

    public function withMessage(string $title): static
    {
        return $this->message($title);
    }

    public function errors(mixed $errors = []): static
    {
        $this->response->errors($errors);

        return $this;
    }

    public function withErrors(mixed $errors = []): static
    {
        return $this->errors($errors);
    }

    public function statusCode(int $statusCode): static
    {
        $this->response->setStatusCode($statusCode);

        return $this;
    }

    public function withStatusCode(int $statusCode): static
    {
        return $this->statusCode($statusCode);
    }

    public static function error(string $message = '', string $title = '', int $statusCode = 500): static
    {
        return new static($message, $title, $statusCode);
    }

    public static function notFound(string $message = '', string $title = ''): static
    {
        return new static($message, $title, ApiResponse::HTTP_NOT_FOUND);
    }

    public static function unprocessable(string $message = '', string $title = ''): static
    {
        return new static($message, $title, ApiResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function unauthorized(string $message = '', string $title = ''): static
    {
        return new static($message, $title, ApiResponse::HTTP_UNAUTHORIZED);
    }

    public static function forbidden(string $message = '', string $title = ''): static
    {
        return new static($message, $title, ApiResponse::HTTP_FORBIDDEN);
    }

    public static function badRequest(string $message = '', string $title = ''): static
    {
        return new static($message, $title, ApiResponse::HTTP_BAD_REQUEST);
    }
}
