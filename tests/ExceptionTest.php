<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use KodePandai\ApiResponse\ApiResponse;
use KodePandai\ApiResponse\Exceptions\ApiException;
use KodePandai\ApiResponse\Exceptions\ApiValidationException;
use KodePandai\ApiResponse\Tests\TestCase;

uses(TestCase::class);

test('dapat menghandle ApiException', function () {
    Route::get('/forbidden', function () {
        throw new ApiException('Forbidden', Response::HTTP_FORBIDDEN);
    });

    $this->getJson('/forbidden')
        ->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonFragment([
            'success' => false,
            'data' => [],
            'errors' => [],
            'title' => 'Error',
            'message' => 'Forbidden',
        ]);
});

test('ApiExecption secara default return 400', function () {
    Route::get('/error', function () {
        throw new ApiException('Just Error');
    });

    $this->getJson('/error')
    ->assertStatus(Response::HTTP_BAD_REQUEST)
    ->assertJsonFragment([
        'success' => false,
        'data' => [],
        'errors' => [],
        'title' => 'Error',
        'message' => 'Just Error',
    ]);
});

test('dapat menghandle ApiValidationException', function () {
    Route::get('/invalid', function () {
        throw new ApiValidationException([
            'handsome' => ['The handsome field is required.'],
        ]);
    });

    $this->getJson('/invalid')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonFragment([
            'success' => false,
            'data' => [],
            'errors' => [
                'handsome' => ['The handsome field is required.'],
            ],
            'title' => 'Validation Error',
            'message' => 'The given data was invalid.',
        ]);
});

test('dapat mengakses route dengan parameter tervalidasi', function () {
    Route::get('/with-param', function (Request $request) {
        ApiResponse::validateOrFail(['handsome' => 'required']);

        return ApiResponse::success($request->all());
    });

    $this->getJson('/with-param')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonFragment([
            'success' => false,
            'data' => [],
            'errors' => [
                'handsome' => ['The handsome field is required.'],
            ],
            'title' => 'Validation Error',
            'message' => 'The given data was invalid.',
        ]);

    $this->getJson('/with-param?handsome=1')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment([
            'success' => true,
            'data' => [
                'handsome' => '1',
            ],
            'errors' => [],
            'title' => 'Success',
            'message' => 'Success',
        ]);
});

test('gagal ketika format error yang diberikan tidak sesuai', function () {
    ApiResponse::error(['X' => [1], 'Y' => 2]);
})->throws(InvalidArgumentException::class);
