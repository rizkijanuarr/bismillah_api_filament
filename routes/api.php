<?php

use Illuminate\Support\Facades\Route;

// LOGIN
Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);

// REGISTER
Route::post('/register', [App\Http\Controllers\Api\Auth\RegisterController::class, 'index']);

//group route with middleware "auth"
Route::group(['middleware' => 'auth:api'], function() {

    //logout
    Route::post('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);

});
