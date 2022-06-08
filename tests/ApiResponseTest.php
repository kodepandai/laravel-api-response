<?php

use Illuminate\Http\Response;
use KodePandai\ApiResponse\ApiResponse;
use KodePandai\ApiResponse\Tests\TestCase;

uses(TestCase::class);

test('ApiResponse menghasilkan header yang sesuai', function () {
    $successResponse = ApiResponse::success()->toResponse(request());
    $errorResponse = ApiResponse::error()->toResponse(request());
    $response = ApiResponse::success()
        ->status(Response::HTTP_CREATED)
        ->addHeader('X-1', 'One')
        ->addHeaders(['X-2' => 'Two', 'X-3' => 'Three'])
        ->toResponse(request());

    expect($successResponse->headers->get('content-type'))->toBe('application/json');
    expect($errorResponse->headers->get('content-type'))->toBe('application/json');
    expect($response)
        ->getStatusCode()->toBe(Response::HTTP_CREATED)
        ->headers->get('X-1')->toBe('One')
        ->headers->get('X-2')->toBe('Two')
        ->headers->get('X-3')->toBe('Three');
});

test('ApiResponse success menghasilkan struktur response success', function () {
    $response = ApiResponse::success(['id' => 1, 'name' => 'Puck'])
        ->title('Sukses Selalu')
        ->message('Syukurlah')
        ->status(Response::HTTP_ACCEPTED)
        ->toResponse(request());

    expect($response)
        ->getStatusCode()->toBe(Response::HTTP_ACCEPTED)
        ->getOriginalContent()->toBe([
            'success' => true,
            'title' => 'Sukses Selalu',
            'message' => 'Syukurlah',
            'data' => ['id' => 1, 'name' => 'Puck'],
            'errors' => [],
        ]);
});

test('ApiResponse error menghasilkan struktur response error', function () {
    $errors = [
        'id' => ['id tidak valid'],
        'nama' => ['nama tidak valid'],
    ];
    $response = ApiResponse::error($errors)
        ->title('Belum Sukses')
        ->message('Tetap Semangat')
        ->status(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->toResponse(request());

    expect($response)
        ->getStatusCode()->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->getOriginalContent()->toBe([
            'success' => false,
            'title' => 'Belum Sukses',
            'message' => 'Tetap Semangat',
            'data' => [],
            'errors' => $errors,
        ]);
});
