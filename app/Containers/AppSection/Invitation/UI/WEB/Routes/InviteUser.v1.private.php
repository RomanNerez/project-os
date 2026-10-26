<?php

use App\Containers\AppSection\Invitation\UI\WEB\Controllers\InviteUserController;
use Illuminate\Support\Facades\Route;

Route::post('users/invitations', InviteUserController::class)
    ->name('users.invite')
    ->middleware(['auth:web']);
