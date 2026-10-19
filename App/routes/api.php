<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ChapterController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\QuestController;
use App\Http\Controllers\Api\V1\UpdateController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('register', [AuthController::class, 'register'])->name('register');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::post('refresh', [AuthController::class, 'refreshToken'])->name('refreshToken');
            Route::get('me', [AuthController::class, 'me'])->name('me');
        });
    });

    Route::prefix('quests')->group(function () {
        Route::get('/', [QuestController::class, 'index'])->name('quests.index');

        Route::prefix('{quest}')->group(function () {
            Route::get('/', [QuestController::class, 'show'])->name('quests.show');

            Route::prefix('/chapters')->group(function () {
                Route::get('/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');
                Route::middleware('auth:sanctum')->group(function () {
                    Route::post('/', [ChapterController::class, 'store'])->name('chapters.store');
                    Route::put('/{chapter}', [ChapterController::class, 'update'])->name('chapters.update');
                });
            });

            Route::prefix('/chats')->group(function () {
                Route::get('/', [ChatController::class, 'index'])->name('chats.index');
                Route::post('/', [ChatController::class, 'store'])->name('chats.store');

                Route::prefix('/{message}')->group(function () {
                    Route::put('/', [ChatController::class, 'update'])->name('chats.update');
                    Route::get('/', [ChatController::class, 'show'])->name('chats.show');
                });
            });
        });

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [QuestController::class, 'store'])->name('quests.store');
            Route::put('/{quest}', [QuestController::class, 'update'])->name('quests.update');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('chapters/{chapter}/updates')->group(function () {
            Route::get('/{updateId}/{type}', [UpdateController::class, 'show'])->name('updates.show');
            Route::post('/', [UpdateController::class, 'store'])->name('updates.store');
            Route::put('/{updateId}', [UpdateController::class, 'update'])->name('updates.update');
            Route::post('/{updateId}/open/{type}', [UpdateController::class, 'open'])->name('updates.open');
            Route::post('/{updateId}/close/{type}', [UpdateController::class, 'close'])->name('updates.close');
        });
    });
});
