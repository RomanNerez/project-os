<?php

use App\Containers\AppSection\User\UI\WEB\Controllers\CreateUserController;
use Illuminate\Support\Facades\Route;

Route::post('users', CreateUserController::class)
    ->name('users.store')
    ->middleware(['auth:web']);
