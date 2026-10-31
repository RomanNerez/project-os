<?php

use App\Containers\AppSection\Authorization\UI\WEB\Controllers\DeleteWorkspaceRoleController;
use Illuminate\Support\Facades\Route;

Route::delete('settings/roles/{role}', DeleteWorkspaceRoleController::class)
    ->name('settings.roles.destroy')
    ->middleware(['auth:web']);
