# Laravel API Response

This package aims to help you standardize all your API responses in
a simple and structured way.

By default, the stucture of the API response looks like this:

```jsonp
{
  "success": true,
  "title": "Users",
  "message": "Active users only",
  "data": [
    // users...
  ],
  "errors": [],
}
```

>**Note**: For now, if you want to customize the response structure, you need to manually extend the `ApiResponse` class and overide the `toResponse` method.

## Install

```sh
$ composer install kodepandai/laravel-api-response
```

## Usage

The `ApiResponse` class implement a [Fluent](https://laravel.com/api/master/Illuminate/Support/Fluent.html)
trait from laravel, so you can chain methods as you like. See the example below.

### Return a success response

```php
use Illuminate\Http\Response;
use KodePandai\ApiResponse\ApiResponse;

ApiResponse::success(['version' => 'v1.0.0']);

ApiResponse::success(['name' => 'Taylor Otwell'])
           ->title('User')
           ->message('User Data');

ApiResponse::success()
           ->statusCode(Response::HTTP_CREATED)
           ->message('Sucessfully create a user.');
```

### Return an error response

By default the `statusCode` for error API response is `HTTP_BAD_REQUEST`.

```php
use Illuminate\Http\Response;
use KodePandai\ApiResponse\ApiResponse;

ApiResponse::error()
           ->title('Failed')
           ->message('Only admin can delete a user.');

ApiResponse::error(['email' => ['The email field is required.']])
           ->statusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
           
ApiResponse::error()
           ->statusCode(Response::HTTP_SERVICE_UNAVAILABLE)
           ->message('Out of service, please comeback later.');

ApiResponse::
```

### Validate or Fail

Use this helper to validate user submitted request then return 
an `ApiResponse::error` response if the validation fails. 
With this helper, now you can use something like `$request->validate()` 
without worrying on how to handle the redirect response or validation errors 
separately.

```php
// without helper

// in the controller, using validate helper.
// if not handled correctly, it can return a redirect reponse instead of json response
$request->validate(['email' => 'required']);

// or using validator
$validator = Validator::make(['email' => 'required']);
if ($validator->fails()) {
    return JsonResponse($validator->errors());
}

//----

// with this helper

// it will automatically return a json response  if validation fails
ApiResponse::validateOrFail([
    'email' => 'required|email',
    'username' => 'required|unique:users,username',
]);
```

### Throwing an Exception

Instead of using `ApiResponse` manually, you can also throw an exception 
and got the same response according to the exception type.

This package provides two exception: `ApiException` and `ApiValidationException`.

* `ApiException`: throw an api error
* `ApiValidationException`: throw a validation error

```php
if (! DB::hasDriver('mysql')) {
  throw new ApiException('Mysql driver not found!', Response::HTTP_INTERNAL_SERVER_ERROR);
}

if ($user->balance <= 100_000) {
  throw new ApiValidationException(['balance' => ['Balance must be greater than 100K']]);
}
```

## Develop

* To test, run `composer test`.
