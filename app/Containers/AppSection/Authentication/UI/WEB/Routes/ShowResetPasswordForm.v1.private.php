<?php

use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ShowResetPasswordFormController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:web')
    ->get('reset-password/{token}', ShowResetPasswordFormController::class)
    ->name('password.reset');
