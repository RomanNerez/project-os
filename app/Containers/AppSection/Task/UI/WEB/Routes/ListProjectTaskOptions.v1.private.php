<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\ListProjectTaskOptionsController;
use Illuminate\Support\Facades\Route;

Route::get('projects/{id}/tasks/options', ListProjectTaskOptionsController::class)
    ->name('projects.tasks.options')
    ->middleware(['auth:web']);
