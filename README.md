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
$ composer require kodepandai/laravel-api-response:^2.0
```

**Requirements:**
* PHP ^8.1
* Laravel ^10.0

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

The above handler will automatically transform any exception and render as ApiResponse.

## Config

Publish config file using vendor:publish command

```sh
$ php artisan vendor:publish --tag=api-response-config
```

## Usage

### Return Response

### Throw Exception

## Develop

- To test run `composer test`.
