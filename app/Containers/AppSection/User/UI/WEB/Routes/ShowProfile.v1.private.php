<?php

use App\Containers\AppSection\User\UI\WEB\Controllers\ShowProfileController;
use Illuminate\Support\Facades\Route;

Route::get('profile', ShowProfileController::class)
    ->name('profile.show')
    ->middleware(['auth:web']);
