<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\UpdateProjectController;
use Illuminate\Support\Facades\Route;

Route::put('projects/{id}', UpdateProjectController::class)
    ->middleware(['auth:web']);

