<?php

use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function() {
    Route::get('/login', 'loginPage')->name('loginPage');
    Route::get('/register', 'registerPage')->name('registerPage');

    Route::post('/login', 'login')->name('login');
    Route::post('/register', 'register')->name('register');
    Route::get('/logout', 'logout')->name('logout');
});

Route::get('/dashboard', function() {
    return view('dashboard');
})->middleware('auth')->name('dashboard');
