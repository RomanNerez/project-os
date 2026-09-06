<?php

use App\Containers\AppSection\Authentication\UI\WEB\Controllers\RegisterUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:web')
        ->post('register', RegisterUserController::class)
        ->name('login');
