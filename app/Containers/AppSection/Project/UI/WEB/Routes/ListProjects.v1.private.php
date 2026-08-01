<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\ListProjectsController;
use Illuminate\Support\Facades\Route;

Route::get('projects', ListProjectsController::class)
    ->name('projects.index')
    ->middleware(['auth:web']);

