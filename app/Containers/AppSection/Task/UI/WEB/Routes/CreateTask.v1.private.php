<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\CreateTaskController;
use Illuminate\Support\Facades\Route;

Route::post('tasks', CreateTaskController::class)
    ->middleware(['auth:web']);
