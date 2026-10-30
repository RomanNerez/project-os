<?php

use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:web')
    ->post('reset-password', ResetPasswordController::class)
    ->name('password.store');
