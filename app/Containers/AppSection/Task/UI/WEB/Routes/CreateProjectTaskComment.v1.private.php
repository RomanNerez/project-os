<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\CreateProjectTaskCommentController;
use Illuminate\Support\Facades\Route;

Route::post('projects/{project}/tasks/{task}/comments', CreateProjectTaskCommentController::class)
    ->middleware(['auth:web'])
    ->scopeBindings()
    ->name('projects.tasks.comments.store');
