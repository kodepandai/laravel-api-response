<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use KodePandai\ApiResponse\Exceptions\ApiException;
use KodePandai\ApiResponse\Exceptions\ApiValidationException;
use KodePandai\ApiResponse\Tests\TestCase;
use function Pest\Laravel\getJson;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

uses(TestCase::class);

it('render an api response when exception is thrown', function () {
    //.
    Route::get('error', fn () => throw new InvalidArgumentException('Hehehe'));

    getJson('error')
        ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
        ->assertJsonStructure(['success', 'title', 'message', 'data'])
        ->assertJsonStructure(['errors' => ['_traces']])
        ->assertJsonPath('message', 'Hehehe');
});

it('does not display message and stack traces on production', function () {
    //.
    Route::get('error', function () {
        App::detectEnvironment(fn () => 'production');

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
    Route::get('400', fn () => throw new ApiException());
    Route::get('502', fn () => throw new ApiException(502, 502));
    Route::get('500', fn () => throw new ApiException(500, 500));
    Route::get('404', fn () => throw new NotFoundHttpException(404));
    Route::get('422', fn () => throw new ApiValidationException());

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
