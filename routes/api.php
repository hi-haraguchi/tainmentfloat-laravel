<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ThoughtController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ReminderController;

// 認証しているユーザー情報を返すAPI
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// ここから下はすべて認証必須
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/titles/search', [TitleController::class, 'search']);

    Route::apiResource('titles', TitleController::class);
    Route::apiResource('tags', TagController::class);
    Route::apiResource('thoughts', ThoughtController::class);
    Route::post('/titles/{title}/thoughts', [ThoughtController::class, 'storeForTitle']);
    Route::get('/titles/{id}/edit-data', [TitleController::class, 'editData']);
    Route::get('/thoughts/{id}/edit-data', [ThoughtController::class, 'editData']);
    Route::get('/tags', [\App\Http\Controllers\TagController::class, 'indexShared']);

    Route::get('/timeline', [TitleController::class, 'timeline']);

    Route::get('/bookmarks/mine', [BookmarkController::class, 'indexMine']);
    Route::post('/bookmarks', [BookmarkController::class, 'store']);
    Route::delete('/bookmarks/{thought}', [BookmarkController::class, 'destroy']);

    Route::get('/remind-setting', [ReminderController::class, 'getRemindSetting']);
    Route::put('/remind-setting', [ReminderController::class, 'updateRemindSetting']);

    Route::get('/intervals', [ReminderController::class, 'getIntervals']);
    Route::put('/intervals', [ReminderController::class, 'updateIntervals']);




});

