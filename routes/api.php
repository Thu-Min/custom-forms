<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function() {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('forms/submit/{id}', [FormController::class, 'submit']);
        Route::resource('forms', FormController::class);
    });
});
