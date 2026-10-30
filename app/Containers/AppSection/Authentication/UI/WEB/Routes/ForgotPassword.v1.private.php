<?php

use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:web')
    ->post('forgot-password', ForgotPasswordController::class)
    ->name('password.forgot');
