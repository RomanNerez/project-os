<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\ShowProjectController;
use Illuminate\Support\Facades\Route;

Route::get('projects/{id}', ShowProjectController::class)
    ->name('projects.show')
    ->middleware(['auth:web']);
