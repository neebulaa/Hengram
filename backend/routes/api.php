<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix("auth")->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::middleware('iscreator')->group(function () {
        Route::delete('/posts/{post:id}', [PostController::class, 'destroy']);
    });

    Route::post('/users/{user:username}/follow', [FollowController::class, 'follow']);
    Route::delete('/users/{user:username}/unfollow', [FollowController::class, 'unfollow']);
    Route::get('/users/{user:username}/following', [FollowController::class, 'following']);
    Route::put('/users/{user:username}/accept', [FollowController::class, 'accept']);
    Route::get('/users/{user:username}/followers', [FollowController::class, 'followers']);
    Route::get("/users", [UserController::class, 'index']);
    Route::get("/users/{user:username}", [UserController::class, 'show']);
});
