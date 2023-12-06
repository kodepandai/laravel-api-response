<?php

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
    'error_code' => \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR,

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
            \Illuminate\Http\Response::HTTP_BAD_GATEWAY,
            \Illuminate\Http\Response::HTTP_BAD_REQUEST,
            \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR,
        ],

        /**
         * Max stack traces shown in the response
         * *Set value to -1 to show all stack traces
         */
        'max_traces_shown' => 10,
    ],

];
