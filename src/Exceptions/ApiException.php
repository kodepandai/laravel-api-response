<?php

namespace KodePandai\ApiResponse\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use KodePandai\ApiResponse\ApiResponse;

class ApiException extends Exception implements Responsable
{
    protected ApiResponse $response;

    public function __construct(string $message = '', string $title = '', ?int $statusCode = null)
    {
        $defaultCode = config('api-response.error_code', 500);

        /** @var ApiResponse $response */
        $response = app('api-response');

        $this->response = match ($statusCode ?: $defaultCode) {
            $defaultCode => $response->error(),
            Response::HTTP_NOT_FOUND => $response->notFound(),
            Response::HTTP_UNPROCESSABLE_ENTITY => $response->unprocessable(),
            Response::HTTP_UNAUTHORIZED => $response->unauthorized(),
            Response::HTTP_FORBIDDEN => $response->forbidden(),
            Response::HTTP_BAD_REQUEST => $response->badRequest(),
            default => /** OTHERS **/ $response->error(),
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

    public function message(string $message): static
    {
        $this->response->message($message);

        return $this;
    }

    public function errors(mixed $errors = []): static
    {
        $this->response->errors($errors);

        return $this;
    }

    public function statusCode(int $statusCode): static
    {
        $this->response->statusCode($statusCode);

        return $this;
    }

    public static function error(string $message = '', string $title = '', ?int $statusCode = null): self
    {
        return new self($message, $title, $statusCode ?: config('api-response.error_code'));
    }

    public static function notFound(string $message = '', string $title = ''): self
    {
        return new self($message, $title, Response::HTTP_NOT_FOUND);
    }

    public static function unprocessable(string $message = '', string $title = ''): self
    {
        return new self($message, $title, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function unauthorized(string $message = '', string $title = ''): self
    {
        return new self($message, $title, Response::HTTP_UNAUTHORIZED);
    }

    public static function forbidden(string $message = '', string $title = ''): self
    {
        return new self($message, $title, Response::HTTP_FORBIDDEN);
    }

    public static function badRequest(string $message = '', string $title = ''): self
    {
        return new self($message, $title, Response::HTTP_BAD_REQUEST);
    }
}
