<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\UpdateProjectTaskController;
use Illuminate\Support\Facades\Route;

Route::patch('projects/{project}/tasks/{task}', UpdateProjectTaskController::class)
    ->middleware(['auth:web'])
    ->scopeBindings()
    ->name('projects.tasks.update');
