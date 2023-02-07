<?php

return [

    /**
     * Set the binding of the api response class.
     * If you want to override the default response class, change here.
     * TODO: make me work!
     */
    // 'api-response-class' => \KodePandai\ApiResponse\ApiResponse::class,

    /**
     * Default error status code, this will be applied in:
     * - return ApiResponse::error()..
     * - throw new ApiException('error')..
     */
    'error-status-code' => \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR,

    /**
     * Response keys shown in the response content.
     * Set value to false to hide key in the response content.
     * TODO: make me work!
     */
    // 'response-keys' => [
    //     'success' => 'success',
    //     'title' => 'title',
    //     'message' => 'message',
    //     'data' => 'data',
    //     'errors' => 'errors',
    // ],

    'traces' => [
        /**
         * Show error stack traces when the response codes in:
         */
        'show-when-status-codes' => [
            \Illuminate\Http\Response::HTTP_BAD_GATEWAY,
            \Illuminate\Http\Response::HTTP_BAD_REQUEST,
            \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR,
        ],

        /**
         * Max stack traces shown int the response errors.
         * Set value to -1 to show all stack traces.
         */
        'max-traces-shown' => 10,
    ],

];
