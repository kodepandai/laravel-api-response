<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use KodePandai\ApiResponse\ApiResponse;
use KodePandai\ApiResponse\Tests\TestCase;

use function Pest\Laravel\getJson;

uses(TestCase::class);

it('returns correct response header', function () {
    //.
    Route::get('api-success', function () {
        return ApiResponse::success();
    });
    Route::get('api-error', function () {
        return ApiResponse::error();
    });
    Route::get('api-puck', function () {
        return ApiResponse::create()
            ->statusCode(Response::HTTP_CREATED)
            ->addHeader('X-1', 'One')
            ->addHeaders(['X-2' => 'Two', 'X-3' => 'Three']);
    });

    getJson('api-success')
        ->assertStatus(Response::HTTP_OK)
        ->assertHeader('content-type', 'application/json');

    getJson('api-error')
        ->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertHeader('content-type', 'application/json');

    getJson('api-puck')
        ->assertStatus(Response::HTTP_CREATED)
        ->assertHeader('content-type', 'application/json')
        ->assertHeader('X-1', 'One')
        ->assertHeader('X-2', 'Two')
        ->assertHeader('X-3', 'Three');
});

it('returns correct json structure for success api response', function () {
    //.
    Route::get('api-puck', function () {
        return ApiResponse::success(['id' => 1, 'name' => 'Puck'])
            ->title('Puck')->message('Puck is awesome');
    });

    getJson('api-puck')
        ->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'success' => true,
            'title' => 'Puck',
            'message' => 'Puck is awesome',
            'data' => ['id' => 1, 'name' => 'Puck'],
            'errors' => [],
        ]);
});

it('returns correct json structure for error api response', function () {
    //.
    $errors = [
        'id' => ['id error one'],
        'name' => ['name error one', 'name error two'],
    ];

    Route::get('api-error', function () use ($errors) {
        return ApiResponse::error($errors)
            ->statusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    });

    getJson('api-error')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'success' => false,
            'title' => 'Error',
            'message' => 'There is an error.',
            'data' => [],
            'errors' => $errors,
        ]);
});

it('fails when the argument $data is not valid', function () {
    ApiResponse::success('Yeah!');
})->throws(InvalidArgumentException::class);

it('fails when the argument $errors is not valid', function () {
    ApiResponse::error(['X' => [1], 'Y' => 2]);
})->throws(InvalidArgumentException::class);
