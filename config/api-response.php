<?php

use Illuminate\Http\Response;

return [

    /**
     * Bind api response class.
     * Update this if you want to override the class.
     */
    'bind_class' => \KodePandai\ApiResponse\ApiResponse::class,

    /**
     * Default error status code, this will be applied in:
     * - return ApiResponse::error()..
     * - throw new ApiException('error')..
     */
    'error_status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,

    /**
     * List of exception status codes.
     * Override the default status code with custom one.
     */
    'exception_status_codes' => [
        \Illuminate\Auth\AuthenticationException::class => Response::HTTP_UNAUTHORIZED,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class => Response::HTTP_NOT_FOUND,
        \Illuminate\Validation\ValidationException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
    ],

    /**
     * Debugging options
     */
    'debug' => [
        /**
         * Show stack traces in the response
         */
        'enabled' => env('APP_DEBUG', true),

        /**
         * Show stack traces only for specific status codes
         *
         * *Set to null to show in all status codes.
         * *Set to false to not show in all status codes.
         */
        'show_traces_in_codes' => [
            Response::HTTP_BAD_GATEWAY,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ],

        /**
         * Max stack traces shown in the response
         * *Set value to -1 to show all stack traces
         */
        'max_traces_shown' => 10,
    ],

];
