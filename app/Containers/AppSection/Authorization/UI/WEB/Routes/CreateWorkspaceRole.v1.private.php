<?php

use App\Containers\AppSection\Authorization\UI\WEB\Controllers\CreateWorkspaceRoleController;
use Illuminate\Support\Facades\Route;

Route::post('settings/roles', CreateWorkspaceRoleController::class)
    ->name('settings.roles.store')
    ->middleware(['auth:web']);
