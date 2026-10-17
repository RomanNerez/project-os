<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\DeleteProjectTaskController;
use Illuminate\Support\Facades\Route;

Route::delete('projects/{project}/tasks/{task}', DeleteProjectTaskController::class)
    ->middleware(['auth:web'])
    ->scopeBindings()
    ->name('projects.tasks.destroy');
