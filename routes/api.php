<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function() {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
    });
});
