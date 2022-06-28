<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
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
