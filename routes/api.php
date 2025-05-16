<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\ErrorController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\LearningLogController;

Route::prefix('v1')->group(function () {
    Route::post('token', [AuthController::class, 'generateToken']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);

        Route::apiResource('projects', ProjectController::class);
        Route::apiResource('projects.tasks', TaskController::class);
        Route::apiResource('projects.tasks.learning-logs', LearningLogController::class);
        Route::apiResource('projects.tasks.errors', ErrorController::class);

        // Standalone routes for retrieving all tasks
        Route::get('tasks', [TaskController::class, 'showAllTasks'])->name('tasks.all');

    });
});
