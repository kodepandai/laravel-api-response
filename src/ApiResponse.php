<?php

namespace KodePandai\ApiResponse;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use InvalidArgumentException;
use KodePandai\ApiResponse\Exceptions\ApiValidationException;

/**
 * @method self statusCode(int $status)
 * @method self isSuccess(bool $isSuccess)
 * @method self title(string $title)
 * @method self message(string $title)
 */
class ApiResponse extends Fluent implements Responsable
{
    /**
     * Creating api response class.
     */
    public function __construct()
    {
        $this->statusCode = Response::HTTP_OK;
        $this->isSuccess = true;
        $this->title = 'Success';
        $this->message = 'Success';
        $this->data = [];
        $this->errors = [];
        $this->headers = ['Content-Type' => 'application/json'];
    }

    /**
     * Add heder to api response.
     */
    public function addHeader(string $key, string $value): self
    {
        $this->headers = array_merge($this->headers, [$key => $value]);

        return $this;
    }

    /**
     * Add multiple headers to api response.
     */
    public function addHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     *  Convert api response class to JsonResponse.
     */
    public function toResponse($request): JsonResponse
    {
        return (new JsonResponse([
                'success' => $this->isSuccess,
                'title' => $this->title,
                'message' => $this->message,
                'data' => $this->data,
                'errors' => $this->errors,
            ]))
            ->setStatusCode($this->statusCode)
            ->withHeaders($this->headers);
    }

    /**
     * Creating new api response instance.
     */
    public static function create(): self
    {
        return new self;
    }

    /**
     * Return a success api response.
     *
     * @param array|Collection|JsonResource|ResourceCollection $data
     */
    public static function success($data = []): self
    {
        return (new self)->data($data);
    }

    /**
     * Return an error api response.
     */
    public static function error(array $errors = []): self
    {
        $valuesNotArray = array_filter($errors, function ($key) {
            return ! is_array($key);
        });

        if (count($valuesNotArray) > 0) {
            throw new InvalidArgumentException('Invalid $errors format');
        }

        return (new self)
            ->isSuccess(false)
            ->statusCode(Response::HTTP_BAD_REQUEST)
            ->title('Error')
            ->message('There is an error.')
            ->errors($errors);
    }

    /**
     * Do validation, if fails return a validation error api response.
     *
     * @throws ApiValidationException
     */
    public static function validateOrFail(
        array $rules,
        array $messages = [],
        array $customAttributes = [],
        Request $request = null
    ): array {
        //.
        $request = $request ?: app(Request::class);

        $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ApiValidationException($validator->errors()->toArray());
        }

        return $validator->validated();
    }

    /**
    * Add data to response and transform according to its type.
    *
    * @param array|Collection|JsonResource|ResourceCollection $data
    */
    public function data($data): self
    {
        if (is_array($data)) {
            $this->attributes['data'] = $data;
        //.
        } elseif ($data instanceof ResourceCollection) {
            $this->attributes['data'] = $data->response()->getData(true)['data'];
        //.
        } elseif ($data instanceof JsonResource) {
            $this->attributes['data'] = json_decode($data->toJson(), true);
        //.
        } elseif ($data instanceof Collection) {
            $this->attributes['data'] = $data->toArray();
        //.
        } else {
            throw new InvalidArgumentException('Invalid $data type');
        }

        return $this;
    }
}
