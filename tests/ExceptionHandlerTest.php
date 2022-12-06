<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use KodePandai\ApiResponse\Exceptions\ApiException;
use KodePandai\ApiResponse\Tests\TestCase;
use function Pest\Laravel\getJson;

uses(TestCase::class);

it('render an api response when exception is thrown', function () {
    //.
    Route::get('error', function () {
        throw new InvalidArgumentException('Hehehe');
    });

    getJson('error')
        ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
        ->assertJsonStructure(['success', 'title', 'message', 'data'])
        ->assertJsonStructure(['errors' => ['_traces']])
        ->assertJsonPath('message', 'Hehehe');
});

it('does not display message and stack traces on production', function () {
    //.
    Route::get('error', function () {
        App::detectEnvironment(function () {
            return 'production';
        });

        throw new InvalidArgumentException('Hihihi');
    });

    getJson('error')
        ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
        ->assertJsonStructure(['success', 'title', 'message', 'data', 'errors'])
        ->assertJsonPath('message', 'No message in production mode.')
        ->assertDontSee('Hihihi')
        ->assertDontSee('_traces');
});

it('does not display traces when 404 not found exception is thrown', function () {
    getJson('404')
        ->assertNotFound()
        ->assertJsonStructure(['success', 'title', 'message', 'data', 'errors'])
        ->assertJsonPath('message', '404 Not Found.')
        ->assertDontSee('_traces');
});

it('can still return json response if exception handler is not configured', function () {
    //.
    Route::get('eee', function () {
        App::singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Orchestra\Testbench\Exceptions\Handler::class,
        );

        throw new ApiException('Error!');
    });

    getJson('eee')
        ->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonPath('message', 'Error!');
});

it('only display traces when response status code in [400, 502, 500]', function () {
    //.
    Route::get('400', function () {
        return abort(Response::HTTP_BAD_REQUEST);
    });
    Route::get('502', function () {
        return abort(Response::HTTP_BAD_GATEWAY);
    });
    Route::get('500', function () {
        return abort(Response::HTTP_INTERNAL_SERVER_ERROR);
    });
    Route::get('404', function () {
        return abort(Response::HTTP_NOT_FOUND);
    });
    Route::get('422', function () {
        throw abort(Response::HTTP_UNPROCESSABLE_ENTITY);
    });

    getJson('400')
        ->assertStatus(Response::HTTP_BAD_REQUEST)
        ->assertJsonStructure(['success', 'title', 'message', 'data', 'errors'])
        ->assertSee('_traces');

    getJson('502')
        ->assertStatus(Response::HTTP_BAD_GATEWAY)
        ->assertJsonStructure(['success', 'title', 'message', 'data', 'errors'])
        ->assertSee('_traces');

    getJson('500')
        ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
        ->assertJsonStructure(['success', 'title', 'message', 'data', 'errors'])
        ->assertSee('_traces');

    getJson('404')
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonStructure(['success', 'title', 'message', 'data', 'errors'])
        ->assertDontSee('_traces');

    getJson('422')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure(['success', 'title', 'message', 'data', 'errors'])
        ->assertDontSee('_traces');
});

it('return 401 for laravel authentication exception', function () {
    //.
    Route::get('dashboard', function () {
        throw new AuthenticationException('Must be authenticated.');
    });

    getJson('dashboard')
        ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonStructure(['success', 'title', 'message', 'data', 'errors'])
        ->assertDontSee('_traces')
        ->assertJsonPath('message', 'Must be authenticated.');
});

it('return 404 for laravel model not found exception', function () {
    //.
    Route::get('model/{id}', function () {
        throw new ModelNotFoundException("No query for Model X");
    });

    getJson('model/1')
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonStructure(['success', 'title', 'message', 'data', 'errors'])
        ->assertDontSee('_traces')
        ->assertJsonPath('message', '404 Not Found.');

    getJson('model/2')
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonStructure(['success', 'title', 'message', 'data', 'errors'])
        ->assertDontSee('_traces')
        ->assertJsonPath('message', '404 Not Found.');
});
