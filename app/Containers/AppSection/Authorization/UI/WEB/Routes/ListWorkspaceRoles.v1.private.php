<?php

use App\Containers\AppSection\Authorization\UI\WEB\Controllers\ListWorkspaceRolesController;
use Illuminate\Support\Facades\Route;

// The settings section opens on its first tab.
Route::redirect('settings', 'settings/roles')
    ->name('settings.index')
    ->middleware(['auth:web']);

// Both tabs list the workspace roles: "roles" manages them, "permissions" is the access matrix.
Route::get('settings/roles', ListWorkspaceRolesController::class)
    ->defaults('tab', 'roles')
    ->name('settings.roles.index')
    ->middleware(['auth:web']);

Route::get('settings/permissions', ListWorkspaceRolesController::class)
    ->defaults('tab', 'permissions')
    ->name('settings.permissions.index')
    ->middleware(['auth:web']);
