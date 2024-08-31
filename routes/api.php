<?php

use Illuminate\Support\Facades\Route;

// LOGIN
Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);

// REGISTER
Route::post('/register', [App\Http\Controllers\Api\Auth\RegisterController::class, 'index']);

//group route with middleware "auth"
Route::group(['middleware' => 'auth:api'], function () {
    // USERS
    Route::apiResource('/users', App\Http\Controllers\Api\UserController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);
    // LOGOUT
    Route::post('/logout', [App\Http\Controllers\Api\Auth\LogoutController::class, 'index']);
});
