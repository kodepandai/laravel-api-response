<?php

namespace KodePandai\ApiResponse;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use KodePandai\ApiResponse\Exceptions\ApiException;
use KodePandai\ApiResponse\Exceptions\ApiValidationException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiExceptionHandler
{
    public static array $defaultStatusCodes = [
        AuthenticationException::class => Response::HTTP_UNAUTHORIZED,
        ModelNotFoundException::class => Response::HTTP_NOT_FOUND,
        ValidationException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
    ];

    /**
     * @return Response|JsonResponse
     */
    public static function render(Throwable $e, Request $request = null)
    {
        $request = $request ?: app(Request::class);

        if ($e instanceof ApiException || $e instanceof ApiValidationException) {
            return $e->toResponse($request);
        }

        if ($e instanceof Responsable) {
            if ($e->toResponse($request) instanceof ApiResponse) {
                return $e->toResponse($request);
            }
        }

        if (method_exists($e, 'getResponse')) {
            if ($e->getResponse() instanceof ApiResponse) {
                return $e->getResponse();
            }
        }

        try {
            $static = new static;

            $traces = $static->getTraces($e, $request);

            return ApiResponse::create()
                ->notSuccessful()
                ->title($static->getTitle($e, $request))
                ->message($static->getMessage($e, $request))
                ->errors($traces ? ['_traces' => $traces] : [])
                ->statusCode($static->getStatusCode($e, $request));
            //
        } catch (\Throwable $e) {
            //
            $handler = app(ExceptionHandler::class);

            $method = new ReflectionMethod($handler, 'renderExceptionResponse');

            $method->setAccessible(true);

            return $method->invoke($handler, $request, $e);
        }
    }

    protected function isProduction(): bool
    {
        return App::isProduction();
    }

    protected function getTitle(Throwable $e, Request $request = null): string
    {
        return __('Error');
    }

    protected function getStatusCode(Throwable $e, Request $request = null): int
    {
        if (isset(static::$defaultStatusCodes[get_class($e)])) {
            return static::$defaultStatusCodes[get_class($e)];
        }

        if ($e instanceof HttpExceptionInterface || method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        if ($e instanceof Responsable) {
            return $e->toResponse($request)->getStatusCode();
        }

        if (method_exists($e, 'getResponse')) {
            return method_exists($e->getResponse(), 'getStatusCode')
                ? $e->getResponse()->getStatusCode()
                : config('laravel-api-response.error-status-code');
        }

        if (! empty(@Response::$statusTexts[$e->getCode()])) {
            return $e->getCode();
        }

        return config('laravel-api-response.error-status-code');
    }

    protected function getMessage(Throwable $e, Request $request = null): string
    {
        if (! empty($e->getMessage()) && ! $this->isProduction()) {
            return $e->getMessage();
        }

        $statusCode = $this->getStatusCode($e, $request);

        return Response::$statusTexts[$statusCode];
    }

    protected function getTraces(Throwable $e, Request $request = null): array
    {
        $shouldDisplayTraces = in_array(
            $this->getStatusCode($e),
            config('laravel-api-response.traces.show-when-status-codes'),
        );

        if ($shouldDisplayTraces && ! $this->isProduction()) {
            return $this->maxTraces() > 0
                ? array_slice($e->getTrace(), 0, $this->maxTraces())
                : $e->getTrace();
        }

        return [];
    }

    protected function maxTraces(): int
    {
        return config('laravel-api-response.max-traces-shown', 10);
    }
}
