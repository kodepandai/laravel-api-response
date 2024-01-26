<?php

namespace KodePandai\ApiResponse\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \KodePandai\ApiResponse\ApiResponse create(mixed $data = [])
 * @method static \KodePandai\ApiResponse\ApiResponse success(mixed $data = [])
 * @method static \KodePandai\ApiResponse\ApiResponse error(mixed $errors = [], int $statusCode = null)
 * @method static \KodePandai\ApiResponse\ApiResponse notFound(mixed $errors = [])
 * @method static \KodePandai\ApiResponse\ApiResponse unprocessable(mixed $errors = [])
 * @method static \KodePandai\ApiResponse\ApiResponse unauthorized(mixed $errors = [])
 * @method static \KodePandai\ApiResponse\ApiResponse forbidden(mixed $errors = [])
 * @method static \KodePandai\ApiResponse\ApiResponse badRequest(mixed $errors = [])
 * @method static \KodePandai\ApiResponse\ApiResponse invalid(string $key, string|array $messages)
 * @method static \KodePandai\ApiResponse\ApiResponse validateOrFail(array $rules, array $messages = [], array $customAttributes = [], ?\Illuminate\Http\Request $request = null): array
 * @method \KodePandai\ApiResponse\ApiResponse statusCode(int $code)
 * @method \KodePandai\ApiResponse\ApiResponse successful()
 * @method \KodePandai\ApiResponse\ApiResponse notSuccessful()
 * @method \KodePandai\ApiResponse\ApiResponse title(string $title)
 * @method \KodePandai\ApiResponse\ApiResponse message(string $message)
 * @method \KodePandai\ApiResponse\ApiResponse errors(array $errors)
 * @method \KodePandai\ApiResponse\ApiResponse data(mixed $data)
 * @method \KodePandai\ApiResponse\ApiResponse addHeader(string $key, string $value)
 * @method \KodePandai\ApiResponse\ApiResponse addHeaders(array $headers)
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
