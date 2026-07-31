<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('didebaneshahr/admin')->name('admin')->group(function(){

        Route::middleware('guest:admin')->group(function(){
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'loginWithPassword'])->name('login.post');

        Route::post('/login/verify', [LoginController::class, 'verifyLoginChallenge'])->name('login.verify');
        });


});

