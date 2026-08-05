<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\CreateProjectController;
use Illuminate\Support\Facades\Route;

Route::post('projects', CreateProjectController::class)
    ->middleware(['auth:web']);

