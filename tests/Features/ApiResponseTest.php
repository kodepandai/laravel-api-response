<?php

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use KodePandai\ApiResponse\Facades\ApiResponse;
use KodePandai\ApiResponse\Tests\TestCase;

use function Pest\Laravel\getJson;

uses(TestCase::class);

// TODO: add more tests

it('returns correct response status', function () {
    //
    Route::get('api-create', fn () => ApiResponse::create());
    Route::get('api-success', fn () => ApiResponse::success());
    Route::get('api-error', fn () => ApiResponse::error());
    Route::get('api-notFound', fn () => ApiResponse::notFound());
    Route::get('api-unprocessable', fn () => ApiResponse::unprocessable());
    Route::get('api-unauthorized', fn () => ApiResponse::unauthorized());
    Route::get('api-forbidden', fn () => ApiResponse::forbidden());
    Route::get('api-badRequest', fn () => ApiResponse::badRequest());

    getJson('api-create')->assertStatus(Response::HTTP_OK);
    getJson('api-success')->assertStatus(Response::HTTP_OK);
    getJson('api-error')->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    getJson('api-notFound')->assertStatus(Response::HTTP_NOT_FOUND);
    getJson('api-unprocessable')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    getJson('api-unauthorized')->assertStatus(Response::HTTP_UNAUTHORIZED);
    getJson('api-forbidden')->assertStatus(Response::HTTP_FORBIDDEN);
    getJson('api-badRequest')->assertStatus(Response::HTTP_BAD_REQUEST);
});

it('returns correct response header', function () {
    //
    Route::get('api-success', fn () => ApiResponse::success());

    Route::get('api-error', fn () => ApiResponse::error());

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
        ->assertStatus(config('api-response.error_status_code'))
        ->assertHeader('content-type', 'application/json');

    getJson('api-puck')
        ->assertStatus(Response::HTTP_CREATED)
        ->assertHeader('content-type', 'application/json')
        ->assertHeader('X-1', 'One')
        ->assertHeader('X-2', 'Two')
        ->assertHeader('X-3', 'Three');
});

it('returns correct json structure for success api response', function () {
    //
    Route::get('api-puck', function () {
        return ApiResponse::success(['id' => 1, 'name' => 'Puck'])
            ->title('Puck')->message('Puck is awesome');
    });

    getJson('api-puck')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('success', true)
        ->assertJsonPath('title', 'Puck')
        ->assertJsonPath('message', 'Puck is awesome')
        ->assertJsonPath('data', ['id' => 1, 'name' => 'Puck'])
        ->assertJsonPath('errors', []);
});

it('returns correct json structure for error api response', function () {
    //
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
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', __('Error'))
        ->assertJsonPath('message', __('Oops! Something went wrong.'))
        ->assertJsonPath('data', [])
        ->assertJsonPath('errors', $errors);
});

it('can handle data of type ResourceCollection|JsonResource|Collection', function () {
    //
    Route::get('r1', function () {
        return ApiResponse::success(collect(['a' => 'b', 'c' => 'd']));
    });

    Route::get('r2', function () {
        return ApiResponse::success(new JsonResource(['x' => 'y', 'z']));
    });

    Route::get('r3', function () {
        return ApiResponse::success(new ResourceCollection([
            new JsonResource(['name' => 'Mario']),
            new JsonResource(['name' => 'Luigi']),
        ]));
    });

    getJson('r1')
        ->assertOk()
        ->assertJsonPath('data', ['a' => 'b', 'c' => 'd']);

    getJson('r2')
        ->assertOk()
        ->assertJsonPath('data', ['x' => 'y', 'z']);

    getJson('r3')
        ->assertOk()
        ->assertJsonPath('data', [['name' => 'Mario'], ['name' => 'Luigi']]);
});

it('fails when the argument $data is not valid', function () {
    ApiResponse::success('Yeah!');
})->throws(InvalidArgumentException::class);
