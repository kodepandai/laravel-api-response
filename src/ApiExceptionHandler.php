<?php

namespace KodePandai\ApiResponse;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
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
    public static function render(Throwable $e, ?Request $request = null)
    {
        $request = $request ?: app(Request::class);

        if ($e instanceof ApiException || $e instanceof ApiValidationException) {
            return $e->toResponse($request);
        }

        if ($e instanceof Responsable) {
            if ($e->toResponse($request) instanceof ApiResponseContract) {
                return $e->toResponse($request);
            }
        }

        if (method_exists($e, 'getResponse')) {
            /** @var ApiValidationException $e as example */
            if ($e->getResponse() instanceof ApiResponseContract) {
                return $e->getResponse();
            }
        }

        try {
            $static = new self;

            $traces = $static->getTraces($e, $request);

            return app('api-response')
                ->create()
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

    protected function getTitle(Throwable $e, ?Request $request = null): string
    {
        return __('api-response::trans.error');
    }

    protected function getStatusCode(Throwable $e, ?Request $request = null): int
    {
        if (isset(static::$defaultStatusCodes[get_class($e)])) {
            return static::$defaultStatusCodes[get_class($e)];
        }

        if ($e instanceof HttpExceptionInterface || method_exists($e, 'getStatusCode')) {
            /** @var HttpExceptionInterface $e as example */
            return $e->getStatusCode();
        }

        if ($e instanceof Responsable) {
            return $e->toResponse($request)->getStatusCode();
        }

        if (method_exists($e, 'getResponse')) {
            /** @var ValidationException $e as example */
            return method_exists($e->getResponse(), 'getStatusCode')
                ? $e->getResponse()->getStatusCode()
                : config('api-response.error_code');
        }

        if (! empty(@Response::$statusTexts[$e->getCode()])) {
            return $e->getCode();
        }

        return config('api-response.error_code');
    }

    protected function getMessage(Throwable $e, ?Request $request = null): string
    {
        if (! empty($e->getMessage()) && $this->debugIsEnabled()) {
            return $e->getMessage();
        }

        $statusCode = $this->getStatusCode($e, $request);

        return Response::$statusTexts[$statusCode];
    }

    protected function getTraces(Throwable $e, ?Request $request = null): array
    {
        $shouldDisplayTraces = in_array(
            $this->getStatusCode($e),
            config('api-response.debug.show_traces_in_codes'),
        );

        if ($shouldDisplayTraces && $this->debugIsEnabled()) {
            return $this->maxTraces() > 0
                ? array_slice($e->getTrace(), 0, $this->maxTraces())
                : $e->getTrace();
        }

        return [];
    }

    protected function debugIsEnabled(): bool
    {
        return config('api-response.debug.enabled');
    }

    protected function maxTraces(): int
    {
        return config('api-response.max_traces_shown', 10);
    }
}
