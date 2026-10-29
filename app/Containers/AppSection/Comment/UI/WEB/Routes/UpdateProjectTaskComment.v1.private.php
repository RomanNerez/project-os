<?php

use App\Containers\AppSection\Comment\UI\WEB\Controllers\UpdateProjectTaskCommentController;
use Illuminate\Support\Facades\Route;

Route::patch('projects/{project}/tasks/{task}/comments/{comment}', UpdateProjectTaskCommentController::class)
    ->middleware(['auth:web'])
    ->scopeBindings()
    ->name('projects.tasks.comments.update');
