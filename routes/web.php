<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Route::get('/docs/api-docs.json', function () {
    $path = storage_path('api-docs/api-docs.json');

    if (!File::exists($path)) {
        abort(404);
    }

    return Response::file($path, [
        'Content-Type' => 'application/json'
    ]);
});

