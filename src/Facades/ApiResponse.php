<?php

namespace KodePandai\ApiResponse\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static ApiResponse create(mixed $data = [])
 * @method static ApiResponse success(mixed $data = [])
 * @method static ApiResponse error(mixed $errors = [], int $statusCode = null)
 * @method static ApiResponse notFound(mixed $errors = [])
 * @method static ApiResponse unprocessable(mixed $errors = [])
 * @method static ApiResponse unauthorized(mixed $errors = [])
 * @method static ApiResponse forbidden(mixed $errors = [])
 * @method static ApiResponse badRequest(mixed $errors = [])
 * @method ApiResponse statusCode(int $code)
 * @method ApiResponse successful()
 * @method ApiResponse notSuccessful()
 * @method ApiResponse title(string $title)
 * @method ApiResponse message(string $message)
 * @method ApiResponse errors(array $errors)
 * @method ApiResponse data(mixed $data)
 * @method ApiResponse addHeader(string $key, string $value)
 * @method ApiResponse addHeaders(array $headers)
 *
 * @see \KodePandai\ApiResponse\ApiResponse
 * @see \KodePandai\ApiResponse\ApiResponseContract
 */
class ApiResponse extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'api-response';
    }
}
