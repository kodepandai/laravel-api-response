<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use KodePandai\ApiResponse\Exceptions\ApiException;
use KodePandai\ApiResponse\Exceptions\ApiValidationException;
use KodePandai\ApiResponse\Tests\TestCase;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(TestCase::class);

// TODO: add more tests

test('ApiException: exception should return an ApiResponse', function () {
    //
    Route::get('api-server-error', fn () => throw ApiException::error());

    Route::get('api-not-found', fn () => throw ApiException::notFound());

    Route::get('api-unprocessable', fn () => throw ApiException::unprocessable());

    Route::get('api-unauthorized', fn () => throw ApiException::unauthorized());

    Route::get('api-forbidden', fn () => throw ApiException::forbidden());

    Route::get('api-bad-request', fn () => throw ApiException::badRequest());

    getJson('api-server-error')
        ->assertServerError()
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'Error')
        ->assertJsonPath('message', 'Oops! Something went wrong.');

    getJson('api-not-found')
        ->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'Not Found')
        ->assertJsonPath('message', 'Resource not found.');

    getJson('api-unprocessable')
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'Validation Error')
        ->assertJsonPath('message', 'The given data was invalid.');

    getJson('api-unauthorized')
        ->assertUnauthorized()
        ->assertJsonPath('title', 'Unauthorized')
        ->assertJsonPath('message', 'Your request is unauthorized.');

    getJson('api-forbidden')
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'Forbidden')
        ->assertJsonPath('message', 'Your request is forbidden.');

    getJson('api-bad-request')
        ->assertBadRequest()
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'Bad Request')
        ->assertJsonPath('message', 'Your request is bad request.');
});

test('ApiException: can set custom title, message, statusCode and errors', function () {
    //
    Route::get('custom-one', function () {
        throw ApiException::error('An ouch occured.', 'Ouch');
    });

    Route::get('custom-two', function () {
        throw ApiException::error()->title('the title')->message('the message');
    });

    Route::get('custom-three', function () {
        throw ApiException::error()->title('the next title')->message('the next message');
    });

    Route::get('custom-four', function () {
        throw ApiException::error()->statusCode(Response::HTTP_CONFLICT);
    });

    Route::get('custom-five', function () {
        throw ApiException::error()->statusCode(Response::HTTP_BAD_GATEWAY);
    });

    Route::get('custom-six', function () {
        throw ApiException::error()->errors(['a' => 'b', 'c' => 'd']);
    });

    Route::get('custom-seven', function () {
        throw ApiException::error()->errors(['x' => 1, 'y' => 2]);
    });

    getJson('custom-one')
        ->assertServerError()
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'Ouch')
        ->assertJsonPath('message', 'An ouch occured.');

    getJson('custom-two')
        ->assertServerError()
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'the title')
        ->assertJsonPath('message', 'the message');

    getJson('custom-three')
        ->assertServerError()
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'the next title')
        ->assertJsonPath('message', 'the next message');

    getJson('custom-four')
        ->assertStatus(Response::HTTP_CONFLICT);

    getJson('custom-five')
        ->assertStatus(Response::HTTP_BAD_GATEWAY);

    getJson('custom-six')
        ->assertServerError()
        ->assertJsonPath('errors.a', 'b')
        ->assertJsonPath('errors.c', 'd');

    getJson('custom-seven')
        ->assertServerError()
        ->assertJsonPath('errors.x', 1)
        ->assertJsonPath('errors.y', 2);
    //
});

test('ApiValidationException: exception should return an ApiResponse', function () {
    //
    Route::get('invalid-one', fn () => throw ApiValidationException::invalid('one'));

    Route::get('invalid-two', fn () => throw ApiValidationException::invalid('two', 'Two not good.'));

    Route::get('invalid-three', fn () => throw ApiValidationException::invalids([
        'count' => PHP_INT_MAX,
        'lorem' => 'ipsum dolor sit ament',
    ]));

    Route::get('invalid-four', fn () => throw ApiValidationException::withErrors([
        'taylor' => 'the creator',
        'laravel' => 'the framework',
        'forge' => ['the server', 'the hosting'],
    ]));

    Route::post('invalid-five', fn () => throw ApiValidationException::withMessages([
        'message-one' => [1, 2, 3, 4, 5],
        'message-two' => [6, 7, 8, 9, 0],
    ]));

    getJson('invalid-one')
        ->assertUnprocessable()
        ->assertInvalid('one')
        ->assertJsonPath('success', false)
        ->assertJsonPath('errors.one.0', 'One is invalid.');

    getJson('invalid-two')
        ->assertUnprocessable()
        ->assertInvalid('two')
        ->assertJsonPath('success', false)
        ->assertJsonPath('errors.two.0', 'Two not good.');

    getJson('invalid-three')
        ->assertUnprocessable()
        ->assertInvalid('lorem')
        ->assertInvalid('count')
        ->assertJsonPath('success', false)
        ->assertJsonPath('errors.count.0', PHP_INT_MAX)
        ->assertJsonPath('errors.lorem.0', 'ipsum dolor sit ament');

    getJson('invalid-four')
        ->assertUnprocessable()
        ->assertInvalid('taylor')
        ->assertInvalid('laravel')
        ->assertJsonPath('success', false)
        ->assertJsonPath('errors.taylor.0', 'the creator')
        ->assertJsonPath('errors.laravel.0', 'the framework')
        ->assertJsonPath('errors.forge.0', 'the server')
        ->assertJsonPath('errors.forge.1', 'the hosting');

    postJson('invalid-five')
        ->assertUnprocessable()
        ->assertInvalid('message-one')
        ->assertInvalid('message-two')
        ->assertJsonPath('success', false)
        ->assertJsonPath('errors.message-one.1', 2)
        ->assertJsonPath('errors.message-one.2', 3)
        ->assertJsonPath('errors.message-two.1', 7)
        ->assertJsonPath('errors.message-two.2', 8);
});

test('it can translate response content based on locale', function () {
    //
    App::setLocale('id');

    Route::get('translate-one', fn () => throw ApiException::error());

    Route::get('translate-two', fn () => throw ApiException::unprocessable());

    Route::get('translate-three', fn () => throw ApiValidationException::invalid('name'));

    getJson('translate-one')
        ->assertServerError()
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'Gagal')
        ->assertJsonPath('message', 'Ups! Terjadi galat.');

    getJson('translate-two')
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'Validasi Gagal')
        ->assertJsonPath('message', 'Data yang diberikan tidak valid.');

    getJson('translate-three')
        ->assertUnprocessable()
        ->assertInvalid('name')
        ->assertJsonPath('success', false)
        ->assertJsonPath('title', 'Validasi Gagal')
        ->assertJsonPath('message', 'Data yang diberikan tidak valid.')
        ->assertJsonPath('errors.name.0', 'Name tidak valid.');
});
