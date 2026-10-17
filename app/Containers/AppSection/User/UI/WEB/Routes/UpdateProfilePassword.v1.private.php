<?php

use App\Containers\AppSection\User\UI\WEB\Controllers\UpdateProfilePasswordController;
use Illuminate\Support\Facades\Route;

Route::put('profile/password', UpdateProfilePasswordController::class)
    ->name('profile.password.update')
    ->middleware(['auth:web']);
