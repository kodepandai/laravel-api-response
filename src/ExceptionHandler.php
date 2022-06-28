<?php

namespace KodePandai\ApiResponse;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ExceptionHandler
{
    /**
     * Render throwable as ApiResponse
     *
     * @return ApiResponse|Response
     */
    public static function renderAsApiResponse(Throwable $e)
    {
        if ($e instanceof Renderable) {
            return $e->render();
        }

        $self = new self;

        $traces = $self->getTraces($e);

        return ApiResponse::error($traces ? ['_traces' => $traces] : [])
            ->message($self->getMessage($e))
            ->statusCode($self->getStatusCode($e));
    }

    /**
     * Get current environtment is production or not.
     *
     * When the environtment is production, it will:
     * - Supress error message
     * - Supress stack traces
     */
    protected function isProduction(): bool
    {
        return App::isProduction();
    }

    /**
     * Get HTTP status code from the Throwable Exception.
     */
    protected function getStatusCode(Throwable $e): int
    {
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $statusCode;
    }

    /**
     * Get message from the Throwable Exception.
     */
    protected function getMessage(Throwable $e): string
    {
        $statusCode = $this->getStatusCode($e);

        if ($statusCode == Response::HTTP_NOT_FOUND) {
            return '404 Not Found.';
        }

        if ($this->isProduction()) {
            return 'No message in production mode.';
        }

        return $e->getMessage() ?: 'Something went wrong.';
    }

    /**
     * Get stack traces from the Throwable Exception.
     */
    protected function getTraces(Throwable $e): array
    {
        $shouldDisplayTraces = in_array(
            $this->getStatusCode($e),
            [
                Response::HTTP_BAD_GATEWAY,
                Response::HTTP_BAD_REQUEST,
                Response::HTTP_INTERNAL_SERVER_ERROR,
            ]
        );

        if ($shouldDisplayTraces && ! $this->isProduction()) {
            return array_slice($e->getTrace(), 0, $this->maxTraces());
        }

        return [];
    }

    /**
     * Maximum line of stack traces will be shown in the response.
     */
    protected function maxTraces(): int
    {
        return 10;
    }
}
