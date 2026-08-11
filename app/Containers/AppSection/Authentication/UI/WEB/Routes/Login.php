<?php

use App\Containers\AppSection\Authentication\UI\WEB\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:web')->group(static function (): void {
    Route::get('/', [LoginController::class, 'showForm'])
        ->name('login.form');

    Route::post('login', LoginController::class)
        ->name('login');
});
