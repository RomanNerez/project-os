<?php

use App\Containers\AppSection\Comment\UI\WEB\Controllers\DeleteProjectTaskCommentController;
use Illuminate\Support\Facades\Route;

Route::delete('projects/{project}/tasks/{task}/comments/{comment}', DeleteProjectTaskCommentController::class)
    ->middleware(['auth:web'])
    ->scopeBindings()
    ->name('projects.tasks.comments.destroy');
