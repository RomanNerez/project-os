<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\UpdateTaskController;
use Illuminate\Support\Facades\Route;

Route::put('tasks/{id}', UpdateTaskController::class)
    ->middleware(['auth:web']);
