<?php

use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ShowForgotPasswordFormController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:web')
    ->get('forgot-password', ShowForgotPasswordFormController::class)
    ->name('password.request');
