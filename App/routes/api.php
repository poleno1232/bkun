<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ChapterController;
use App\Http\Controllers\Api\V1\ChatController;
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

            Route::prefix('/chats')->group(function () {
                Route::get('/', [ChatController::class, 'index']);
                Route::post('/', [ChatController::class, 'store']);

                Route::prefix('/{message}')->group(function () {
                    Route::put('/', [ChatController::class, 'update']);
                    Route::get('/', [ChatController::class, 'show']);
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
            Route::get('/{updateId}/{type}', [UpdateController::class, 'show']);
            Route::post('/', [UpdateController::class, 'store']);
            Route::put('/{updateId}', [UpdateController::class, 'update']);
            Route::post('/{updateId}/open', [UpdateController::class, 'open']);
            Route::post('/{updateId}/close', [UpdateController::class, 'close']);
        });
    });
});
