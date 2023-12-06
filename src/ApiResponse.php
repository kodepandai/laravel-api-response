<?php

namespace KodePandai\ApiResponse;

use ArrayObject;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use KodePandai\ApiResponse\Exceptions\ApiValidationException;

class ApiResponse extends JsonResponse implements ApiResponseContract
{
    protected bool $isSuccess = true;

    protected string $title;

    protected string $message;

    protected array $errors = [];

    protected mixed $originalData = [];

    public function __construct(string $message = '', string $title = '')
    {
        $this->title = $title ?: ('api-response::trans.success');
        $this->message = $message ?: ('api-response::trans.successful');

        parent::__construct();
    }

    public function validateOrFail(
        array $rules,
        array $messages = [],
        array $customAttributes = [],
        ?Request $request = null
    ): array {
        //
        $request = $request ?: app(Request::class);

        $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);

        throw_if($validator->fails(), new ApiValidationException($validator));

        return $validator->validated();
    }

    public function create(mixed $data = []): static
    {
        return $this->statusCode(static::HTTP_OK)->data($data);
    }

    public function success(mixed $data = []): static
    {
        return static::create($data);
    }

    public function error(mixed $errors = [], ?int $statusCode = null): static
    {
        return static::create()
            ->notSuccessful()
            ->title(__('api-response::trans.error'))
            ->message(__('api-response::trans.something_went_wrong'))
            ->errors($errors)
            ->statusCode($statusCode ?: config('api-response.error_code'));
    }

    public function notFound(mixed $errors = []): static
    {
        return static::error($errors, static::HTTP_NOT_FOUND)
            ->title(__('api-response::trans.not_found'))
            ->message(__('api-response::trans.resource_not_found'));
    }

    public function unprocessable(mixed $errors = []): static
    {
        return static::error($errors, static::HTTP_UNPROCESSABLE_ENTITY)
            ->title(__('api-response::trans.validation_error'))
            ->message(__('api-response::trans.given_data_was_invalid'));
    }

    public function unauthorized(mixed $errors = []): static
    {
        return static::error($errors, static::HTTP_UNAUTHORIZED)
            ->title(__('api-response::trans.unauthorized'))
            ->message(__('api-response::trans.request_is_unauthorized'));
    }

    public function forbidden(mixed $errors = []): static
    {
        return static::error($errors, static::HTTP_FORBIDDEN)
            ->title(__('api-response::trans.forbidden'))
            ->message(__('api-response::trans.request_is_forbidden'));
    }

    public function badRequest(mixed $errors = []): static
    {
        return static::error($errors, static::HTTP_BAD_REQUEST)
            ->title(__('api-response::trans.bad_request'))
            ->message(__('api-response::trans.request_is_bad_request'));
    }

    public function statusCode(int $code): static
    {
        return $this->setStatusCode($code);
    }

    public function getIsSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function setIsSuccess(bool $isSuccess): static
    {
        $this->isSuccess = $isSuccess;

        return $this->synchronizeData();
    }

    public function successful(): static
    {
        return $this->setIsSuccess(true);
    }

    public function notSuccessful(): static
    {
        return $this->setIsSuccess(false);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this->synchronizeData();
    }

    public function title(string $title): static
    {
        return $this->setTitle($title);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this->synchronizeData();
    }

    public function message(string $message): static
    {
        return $this->setMessage($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): static
    {
        $this->errors = $errors;

        return $this->synchronizeData();
    }

    public function errors(array $errors): static
    {
        return $this->setErrors($errors);
    }

    public function synchronizeData(): static
    {
        return parent::setData([
            'success' => $this->getIsSuccess(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'data' => $this->getOriginalData(),
            'errors' => $this->getErrors(),
        ]);
    }

    public function getOriginalData(): mixed
    {
        return $this->originalData;
    }

    public function setOriginalData(mixed $data): static
    {
        $this->originalData = $this->transformData($data);

        return $this->synchronizeData();
    }

    public function data(mixed $data): static
    {
        return $this->setOriginalData($data);
    }

    public function transformData(mixed $data = []): array
    {
        if (is_array($data)) {
            $data = $data;
        //
        } elseif ($data instanceof ArrayObject) {
            $data = (array) $data;
        //
        } elseif ($data instanceof ResourceCollection) {
            $data = ($data->resource instanceof AbstractPaginator)
                ? $data->response()->getData(true)
                : json_decode($data->toJson(), true);
        //
        } elseif ($data instanceof JsonResource || method_exists($data, 'toJson')) {
            $data = json_decode($data->toJson(), true);
        //
        } elseif ($data instanceof Arrayable || method_exists($data, 'toArray')) {
            $data = $data->toArray();
        //
        } else {
            throw new InvalidArgumentException('Unsupported $data type for API Response');
        }

        return $data;
    }

    public function addHeader(string $key, string $value): static
    {
        return $this->header($key, $value);
    }

    public function addHeaders(array $headers): static
    {
        return $this->withHeaders($headers);
    }
}
