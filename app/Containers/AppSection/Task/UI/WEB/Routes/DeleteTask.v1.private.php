<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\DeleteTaskController;
use Illuminate\Support\Facades\Route;

Route::delete('tasks/{id}', DeleteTaskController::class)
    ->middleware(['auth:web']);
