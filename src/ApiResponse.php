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

class ApiResponse extends JsonResponse
{
    protected bool $isSuccess = true;

    protected string $title;

    protected string $message;

    protected array $errors = [];

    protected mixed $originalData = [];

    public function __construct(string $message = '', string $title = '')
    {
        $this->title = $title ?: __('Success');
        $this->message = $message ?: __('Successful');

        parent::__construct();
    }

    public static function validateOrFail(
        array $rules,
        array $messages = [],
        array $customAttributes = [],
        Request $request = null
    ): array {
        //
        $request = $request ?: app(Request::class);

        $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ApiValidationException($validator);
        }

        return $validator->validated();
    }

    public static function create(mixed $data = []): static
    {
        return (new static)->statusCode(static::HTTP_OK)->data($data);
    }

    public static function success(mixed $data = []): static
    {
        return static::create($data);
    }

    public static function error(mixed $errors = [], int $statusCode = 500): static
    {
        return static::create()
            ->notSuccessful()
            ->title(__('Error'))
            ->message(__('Oops! Something went wrong.'))
            ->errors($errors)
            ->statusCode($statusCode);
    }

    public static function notFound(mixed $errors = []): static
    {
        return static::error($errors, static::HTTP_NOT_FOUND)
                     ->title(__('Not Found'))
                     ->message(__('Resource not found.'));
    }

    public static function unprocessable(mixed $errors = []): static
    {
        return static::error($errors, static::HTTP_UNPROCESSABLE_ENTITY)
                     ->title(__('Validation Error'))
                     ->message(__('The given data was invalid.'));
    }

    public static function unauthorized(mixed $errors = []): static
    {
        return static::error($errors, static::HTTP_UNAUTHORIZED)
                     ->title(__('Unauthorized'))
                     ->message(__('Your request is unauthorized.'));
    }

    public static function forbidden(mixed $errors = []): static
    {
        return static::error($errors, static::HTTP_FORBIDDEN)
                     ->title(__('Forbidden'))
                     ->message(__('Your request is forbidden.'));
    }

    public static function badRequest(mixed $errors = []): static
    {
        return static::error($errors, static::HTTP_BAD_REQUEST)
                     ->title(__('Bad Request'))
                     ->message(__('Your request is bad request.'));
    }

    public function statusCode(int $code): static
    {
        return $this->setStatusCode($code);
    }

    protected function withStatusCode(int $code): static
    {
        return $this->setStatusCode($code);
    }

    protected function setIsSuccess(bool $isSuccess): static
    {
        $this->isSuccess = $isSuccess;

        return $this;
    }

    protected function getIsSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function successful(): static
    {
        return $this->setIsSuccess(true);
    }

    public function notSuccessful(): static
    {
        return $this->setIsSuccess(false);
    }

    protected function setTitle(string $title): static
    {
        $this->title = $title;

        return $this->syncData();
    }

    protected function getTitle(): string
    {
        return $this->title;
    }

    public function title(string $title): static
    {
        return $this->setTitle($title);
    }

    public function withTitle(string $title): static
    {
        return $this->setTitle($title);
    }

    protected function setMessage(string $message): static
    {
        $this->message = $message;

        return $this->syncData();
    }

    protected function getMessage(): string
    {
        return $this->message;
    }

    public function message(string $message): static
    {
        return $this->setMessage($message);
    }

    public function withMessage(string $message): static
    {
        return $this->setMessage($message);
    }

    protected function setErrors(array $errors): static
    {
        $this->errors = $errors;

        return $this->syncData();
    }

    protected function getErrors(): array
    {
        return $this->errors;
    }

    public function errors(array $errors): static
    {
        return $this->setErrors($errors);
    }

    public function withErrors(array $errors): static
    {
        return $this->setErrors($errors);
    }

    public function setData(mixed $data = []): static
    {
        if (is_array($data)) {
            $data = $data;
            //
        } elseif ($data instanceof ArrayObject) {
            $data = (array) $data;
            //
        } elseif ($data instanceof ResourceCollection) {
            $data = ($data->resource instanceof AbstractPaginator)
                ? $data->response()->getData() : json_decode($data->toJson());
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

        return $this->setOriginalData($data);
    }

    public function setOriginalData(mixed $data = []): static
    {
        $this->originalData = $data;

        return $this->syncData();
    }

    public function getOriginalData(): mixed
    {
        return $this->originalData;
    }

    protected function syncData(): static
    {
        return parent::setData([
            'success' => $this->getIsSuccess(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'data' => $this->getOriginalData(),
            'errors' => $this->getErrors(),
        ]);
    }

    public function data(mixed $data): static
    {
        return $this->setData($data);
    }

    public function withData(mixed $data): static
    {
        return $this->setData($data);
    }
}
