<?php

use App\Containers\AppSection\Authorization\UI\WEB\Controllers\UpdateWorkspaceRoleController;
use Illuminate\Support\Facades\Route;

Route::put('settings/roles/{role}', UpdateWorkspaceRoleController::class)
    ->name('settings.roles.update')
    ->middleware(['auth:web']);
