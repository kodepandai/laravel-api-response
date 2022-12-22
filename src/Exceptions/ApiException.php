<?php

namespace KodePandai\ApiResponse\Exceptions;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use KodePandai\ApiResponse\ApiResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException implements Responsable, Renderable
{
    public $title = '';

    public $errors = [];

    /**
     * Throw an error api exception.
     *
     * @param string $message    The message
     * @param int    $statusCode The HTTP status code
     * @param array  $errors     The errors
     */
    public function __construct(
        string $message = 'There is an error.',
        int $statusCode = Response::HTTP_BAD_REQUEST,
        array $errors = []
    ) {
        $this->message = $message;
        $this->errors = $errors;

        parent::__construct($statusCode, $message);
    }

    /**
     * Convert exception to json response.
     */
    public function toResponse($request): JsonResponse
    {
        return $this->getResponse($request);
    }

    /**
     * Render exception as json response.
     */
    public function render(Request $request = null): JsonResponse
    {
        return $this->getResponse($request);
    }

    /**
     * Get the response.
     */
    public function getResponse(Request $request = null): JsonResponse
    {
        $request = $request ?? request();

        return ApiResponse::error()
            ->message($this->getMessage())
            ->statusCode($this->getStatusCode())
            ->toResponse($request);
    }
}
