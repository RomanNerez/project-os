<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\CreateProjectTaskController;
use Illuminate\Support\Facades\Route;

Route::post('projects/{project}/tasks', CreateProjectTaskController::class)
    ->middleware(['auth:web'])
    ->name('projects.tasks.store');
