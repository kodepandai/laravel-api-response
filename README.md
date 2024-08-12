# Laravel API Response v2

A helper package to return JSON Api Response in structured way.

By default, the structure of the response will look like this:

```jsonc
{
  "success": true, // it was successfull or not
  "title": "Users", // the title/headline/section 
  "message": "Active users only", // the message/description/highlight
  "data": { // if it was successfull
    // profile..
    // users..
    // products..
    // etc..
  },
  "errors": { // if it was not successfull
    // validation errors..
    // any other errors..
  }
}
```

Example:

```jsonc
{
  "success": true,
  "title": "Users",
  "message": "Succesfully create a user",
  "data": {
    "id": 1,
    "name": "John Doe",
    "address": "4th Semarang Raya",
  },
}
```

## Install

```sh
$ composer require kodepandai/laravel-api-response:dev-beta
```

**Requirements:**
* PHP ^8.1
* Laravel ^10.0

**Laravel ^11**

After installation, register api response handler in `bootstrap/app.php`

```php
use KodePandai\ApiResponse\ApiExceptionHandler;

return Application::configure(basePath: dirname(__DIR__))
    //...
    ->withExceptions(function (Exceptions $exceptions) {
        // dont report any api response exception
        $exceptions->dontReport([
            \KodePandai\ApiResponse\Exceptions\ApiException::class,
            \KodePandai\ApiResponse\Exceptions\ApiValidationException::class,
        ]);
        // api response exception handler for /api
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->wantsJson() || $request->is('*api*')) {
                return ApiExceptionHandler::render($e, $request);
            }
        });
    });
```

**Laravel ^10**

After installation, register api response handler in `app/Exceptions/Handler.php`

```php
use KodePandai\ApiResponse\ApiExceptionHandler;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        \KodePandai\ApiResponse\Exceptions\ApiException::class,
        \KodePandai\ApiResponse\Exceptions\ApiValidationException::class,
    ];

    public function register()
    {
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->wantsJson() || $request->is('*api*')) {
                return ApiExceptionHandler::render($e, $request);
            }
        });
    }
}
```

The above handler will automatically transform any exception and render as ApiResponse json response.

## Config

Publish config file using vendor:publish command

```sh
$ php artisan vendor:publish --tag=api-response-config
```

## Usage

TODO

### Return Response

TODO

### Throw Exception

TODO

### Overriding response structure

TODO

## Develop

- To test run `composer test`.
