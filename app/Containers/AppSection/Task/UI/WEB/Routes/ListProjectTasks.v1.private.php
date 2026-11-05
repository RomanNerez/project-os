<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\ListProjectTasksController;
use Illuminate\Support\Facades\Route;

Route::get('projects/{project}/tasks', ListProjectTasksController::class)
    ->name('projects.tasks.index')
    ->middleware(['auth:web']);
