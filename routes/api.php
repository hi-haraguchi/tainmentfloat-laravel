<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ThoughtController;

// 認証しているユーザー情報を返すAPI
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// ここから下はすべて認証必須
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('titles', TitleController::class);
    Route::apiResource('tags', TagController::class);
    Route::apiResource('thoughts', ThoughtController::class);
    Route::post('/titles/{title}/thoughts', [ThoughtController::class, 'storeForTitle']);

});

