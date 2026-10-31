<?php

use App\Containers\AppSection\Authorization\UI\WEB\Controllers\UpdateUserWorkspaceRoleController;
use Illuminate\Support\Facades\Route;

Route::put('users/{user}/role', UpdateUserWorkspaceRoleController::class)
    ->name('users.role.update')
    ->middleware(['auth:web']);
