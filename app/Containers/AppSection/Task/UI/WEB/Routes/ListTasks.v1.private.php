<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\ListTasksController;
use Illuminate\Support\Facades\Route;

Route::get('tasks', ListTasksController::class)
    ->name('tasks.index')
    ->middleware(['auth:web']);
