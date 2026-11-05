<?php

use App\Containers\AppSection\Finance\UI\WEB\Controllers\ListFinanceTasksController;
use Illuminate\Support\Facades\Route;

Route::get('projects/{project}/finances/tasks', ListFinanceTasksController::class)
    ->name('projects.finances.tasks')
    ->middleware(['auth:web']);
