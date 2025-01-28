<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\FormController;
use App\Models\Form;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function() {
    Route::get('/login', 'loginPage')->name('loginPage');
    Route::get('/register', 'registerPage')->name('registerPage');

    Route::post('/login', 'login')->name('login');
    Route::post('/register', 'register')->name('register');
    Route::get('/logout', 'logout')->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', function() {
        $forms = Form::all();

        return view('dashboard', compact('forms'));
    })->name('dashboard');

    Route::resource('forms', FormController::class);
});
