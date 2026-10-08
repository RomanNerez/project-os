<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\UpdateProjectController;
use Illuminate\Support\Facades\Route;

Route::put('projects/{project}', UpdateProjectController::class)
    ->middleware(['auth:web']);

