<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\ListProjectMembersController;
use Illuminate\Support\Facades\Route;

Route::get('projects/{id}/members', ListProjectMembersController::class)
    ->name('projects.members.index')
    ->middleware(['auth:web']);
