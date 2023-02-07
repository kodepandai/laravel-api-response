<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use KodePandai\ApiResponse\ApiResponse;
use KodePandai\ApiResponse\Exceptions\ApiException;
use KodePandai\ApiResponse\Exceptions\ApiValidationException;
use KodePandai\ApiResponse\Tests\TestCase;
use function Pest\Laravel\getJson;

uses(TestCase::class);

it('can throw an ApiException then returns an ApiResponse', function () {
    //
    Route::get('api-server-error', fn () => throw ApiException::error());
    Route::get('api-not-found', fn () => throw ApiException::notFound());
    Route::get('api-unprocessable', fn () => throw ApiException::unprocessable());
    Route::get('api-unauthorized', fn () => throw ApiException::unauthorized());
    Route::get('api-forbidden', fn () => throw ApiException::forbidden());
    Route::get('api-bad-request', fn () => throw ApiException::badRequest());

    getJson('api-server-error')
        ->assertServerError()
        ->assertJsonPath('title', 'Error')
        ->assertJsonPath('message', 'Oops! Something went wrong.');

    getJson('api-not-found')
        ->assertNotFound()
        ->assertJsonPath('title', 'Not Found')
        ->assertJsonPath('message', 'Resource not found.');

    getJson('api-unprocessable')
        ->assertUnprocessable()
        ->assertJsonPath('title', 'Validation Error')
        ->assertJsonPath('message', 'The given data was invalid.');

    getJson('api-unauthorized')
        ->assertUnauthorized()
        ->assertJsonPath('title', 'Unauthorized')
        ->assertJsonPath('message', 'Your request is unauthorized.');

    getJson('api-forbidden')
        ->assertForbidden()
        ->assertJsonPath('title', 'Forbidden')
        ->assertJsonPath('message', 'Your request is forbidden.');

    getJson('api-bad-request')
        ->assertBadRequest()
        ->assertJsonPath('title', 'Bad Request')
        ->assertJsonPath('message', 'Your request is bad request.');
});

it('can understand user translations', function () {
    //
    App::setLocale('id');

    Route::get('api-error', fn () => ApiException::error());
    Route::get('api-unprocessable', fn () => ApiException::unprocessable());

    getJson('api-error')
        ->assertServerError()
        ->assertJsonFragment([
            'title' => 'Gagal',
            'message' => 'Ups! Terjadi galat.',
        ]);

    getJson('api-unprocessable')
        ->assertUnprocessable()
        ->assertJsonFragment([
            'title' => 'Validasi Gagal',
            'message' => 'Data yang diberikan tidak valid.',
        ]);
});

return;

it('returns HTTP_BAD_REQUEST for ApiException by default', function () {
    //
    Route::get('api-error', function () {
        throw new ApiException('Just Error');
    });

    getJson('api-error')
        ->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonFragment([
            'success' => false,
            'title' => 'Error',
            'message' => 'Just Error',
            'data' => [],
            'errors' => [],
        ]);
});

it('can handle ApiValidationException', function () {
    //
    Route::get('invalid', function () {
        throw new ApiValidationException([
            'handsome' => ['The handsome field is required.'],
        ]);
    });

    getJson('invalid')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'success' => false,
            'title' => 'Validation Error',
            'message' => 'The given data was invalid.',
            'data' => [],
            'errors' => [
                'handsome' => ['The handsome field is required.'],
            ],
        ]);
});

it('can access route with validated parameters', function () {
    //
    Route::get('with-param', function (Request $request) {
        ApiResponse::validateOrFail(['handsome' => 'required']);

        return ApiResponse::success($request->all());
    });

    getJson('with-param')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'success' => false,
            'title' => 'Validation Error',
            'message' => 'The given data was invalid.',
            'data' => [],
            'errors' => [
                'handsome' => ['The handsome field is required.'],
            ],
        ]);

    getJson('/with-param?handsome=1')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment([
            'success' => true,
            'title' => 'Success',
            'message' => 'Success',
            'data' => [
                'handsome' => '1',
            ],
            'errors' => [],
        ]);
});
