<?php

use App\Containers\AppSection\User\UI\WEB\Controllers\UpdateProfileController;
use Illuminate\Support\Facades\Route;

// POST (not PUT): the form is sent as multipart when it carries an avatar file.
Route::post('profile', UpdateProfileController::class)
    ->name('profile.update')
    ->middleware(['auth:web']);
