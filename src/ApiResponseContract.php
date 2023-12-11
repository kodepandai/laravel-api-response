<?php

namespace KodePandai\ApiResponse;

use Illuminate\Http\Request;
use KodePandai\ApiResponse\Exceptions\ApiValidationException;

interface ApiResponseContract
{
    /**
     * @throws ApiValidationException
     */
    public function validateOrFail(
        array $rules,
        array $messages = [],
        array $customAttributes = [],
        ?Request $request = null,
    ): array;

    public function create(mixed $data = []): static;

    public function success(mixed $data = []): static;

    public function error(mixed $errors = [], ?int $statusCode = null): static;

    public function notFound(mixed $errors = []): static;

    public function unprocessable(mixed $errors = []): static;

    public function unauthorized(mixed $errors = []): static;

    public function forbidden(mixed $errors = []): static;

    public function badRequest(mixed $errors = []): static;

    public function statusCode(int $code): static;

    public function getIsSuccess(): bool;

    public function setIsSuccess(bool $isSuccess): static;

    public function successful(): static;

    public function notSuccessful(): static;

    public function title(string $title): static;

    public function message(string $message): static;

    public function errors(array $errors): static;

    public function getOriginalData(): mixed;

    public function setOriginalData(mixed $data): static;

    public function data(mixed $data): static;

    public function transformData(mixed $data = []): array;

    public function addHeader(string $key, string $value): static;

    public function addHeaders(array $data): static;
}
