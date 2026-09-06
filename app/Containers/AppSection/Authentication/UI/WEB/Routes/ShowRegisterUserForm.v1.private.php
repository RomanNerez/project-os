<?php

use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ShowRegisterUserFormController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:web')
    ->get('/register', ShowRegisterUserFormController::class)
    ->name('register.form');
