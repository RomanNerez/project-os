<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\CreateTaskCommentController;
use Illuminate\Support\Facades\Route;

Route::post('tasks/{taskId}/comment', CreateTaskCommentController::class)
    ->middleware(['auth:web']);
