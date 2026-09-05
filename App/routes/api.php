<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ChapterController;
use App\Http\Controllers\Api\V1\QuestController;
use App\Http\Controllers\Api\V1\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refreshToken']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    Route::prefix('quests')->group(function () {
        Route::get('/', [QuestController::class, 'index']);
        Route::get('/{quest}', [QuestController::class, 'show']);

        Route::prefix('{quest}')->group(function () {
            Route::get('/', [QuestController::class, 'show']);

            Route::prefix('/chapters')->group(function () {
                Route::get('/{chapter}', [ChapterController::class, 'show']);
                Route::middleware('auth:sanctum')->group(function () {
                    Route::post('/', [ChapterController::class, 'store']);
                    Route::put('/{chapter}', [ChapterController::class, 'update']);
                });
            });
        });

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [QuestController::class, 'store']);
            Route::put('/{quest}', [QuestController::class, 'update']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('chapters/{chapter}/updates')->group(function () {
            Route::post('/', [UpdateController::class, 'store']);
        });
    });
});
