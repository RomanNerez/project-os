<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\UpdateTaskStatusController;
use Illuminate\Support\Facades\Route;

Route::patch('tasks/{id}/status', UpdateTaskStatusController::class)
    ->middleware(['auth:web']);
