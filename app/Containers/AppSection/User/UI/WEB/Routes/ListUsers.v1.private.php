<?php

use App\Containers\AppSection\User\UI\WEB\Controllers\ListUsersController;
use Illuminate\Support\Facades\Route;

Route::get('users', ListUsersController::class)
    ->name('users.index')
    ->middleware(['auth:web']);
