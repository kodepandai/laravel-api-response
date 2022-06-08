<?php

namespace KodePandai\ApiResponse;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use InvalidArgumentException;
use KodePandai\ApiResponse\Exceptions\ApiValidationException;

/**
 * @method self status(int $status)
 * @method self title(string $title)
 * @method self message(string $title)
 */
class ApiResponse extends Fluent implements Responsable
{
    public function __construct()
    {
        $this->status = Response::HTTP_OK;
        $this->isSuccess = true;
        $this->title = 'Success';
        $this->message = 'Success';
        $this->data = [];
        $this->errors = [];
        $this->headers = ['Content-Type' => 'application/json'];
    }

    /**
     * Tambah response header.
     */
    public function addHeader(string $key, string $value): self
    {
        $this->headers = array_merge($this->headers, [$key => $value]);

        return $this;
    }

    /**
     * Menambahkan beberapa response header.
     */
    public function addHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function toResponse($request): Response
    {
        return (new Response([
            'success' => $this->isSuccess,
            'title' => $this->title,
            'message' => $this->message,
            'data' => $this->data,
            'errors' => $this->errors,
        ]))->setStatusCode($this->status)->withHeaders($this->headers);
    }

    /**
     * Berikan response api dengan status sukses.
     */
    public static function success(mixed $data = []): self
    {
        return (new self)->data($data);
    }

    /**
     * Berikan response api dengan status gagal.
     */
    public static function error(array $errors = []): self
    {
        $valuesNotArray = array_filter($errors, fn ($key) => ! is_array($key));

        if (count($valuesNotArray) > 0) {
            throw new InvalidArgumentException('Invalid $errors format');
        }

        $response = new self;
        $response->attributes['isSuccess'] = false;

        return $response->title('Error')
            ->message('Something went wrong')
            ->status(Response::HTTP_BAD_REQUEST)
            ->errors($errors);
    }

    /**
     * Lakukan validasi, jika tidak valid diberikan response api gagal.
     *
     * @throws KodePandai\ApiResponse\Exceptions\ApiValidationException
     */
    public static function validateOrFail(array $rules, array $messages = [], array $customAttributes = []): void
    {
        $validator = Validator::make(request()->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ApiValidationException($validator->errors()->toArray());
        }
    }

    /**
     * Tambah data dan transform sesuai tipenya
     */
    public function data(mixed $data): self
    {
        $this->attributes['data'] = match (true) {
            $data instanceof ResourceCollection => $data->response()->getData(true),
            $data instanceof JsonResource => json_decode($data->toJson(), true),
            $data instanceof Collection => $data->toArray(),
            default => $data
        };

        return $this;
    }
}
