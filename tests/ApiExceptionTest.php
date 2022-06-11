<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use KodePandai\ApiResponse\ApiResponse;
use KodePandai\ApiResponse\Exceptions\ApiException;
use KodePandai\ApiResponse\Exceptions\ApiValidationException;
use KodePandai\ApiResponse\Tests\TestCase;

use function Pest\Laravel\getJson;

uses(TestCase::class);

it('can handle ApiException', function () {
    //.
    Route::get('api-forbidden', function () {
        throw new ApiException('Forbidden', Response::HTTP_FORBIDDEN);
    });

    getJson('api-forbidden')
        ->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJson([
            'success' => false,
            'title' => 'Error',
            'message' => 'Forbidden',
            'data' => [],
            'errors' => [],
        ]);
});

it('returns HTTP_BAD_REQUEST for ApiException by default', function () {
    //.
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
    //.
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
    //.
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
