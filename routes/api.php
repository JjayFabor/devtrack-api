<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\ErrorController;
use App\Http\Controllers\Api\V1\ApiKeyController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\LearningLogController;

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        Route::apiResource('api-keys', ApiKeyController::class);

        Route::middleware('api.key.throttle')->group(function () {
            Route::put('api-keys/{apiKey}/revoke', [ApiKeyController::class, 'revoke']);

            Route::apiResource('projects', ProjectController::class);
            Route::apiResource('projects.tasks', TaskController::class);
            Route::apiResource('projects.tasks.learning-logs', LearningLogController::class);
            Route::apiResource('projects.tasks.errors', ErrorController::class);

            // Standalone route for retrieving all tasks
            Route::get('tasks', [TaskController::class, 'showAllTasks'])->name('tasks.all');
        });
    });
});
