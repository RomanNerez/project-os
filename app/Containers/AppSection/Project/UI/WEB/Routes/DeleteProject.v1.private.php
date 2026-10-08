<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\DeleteProjectController;
use Illuminate\Support\Facades\Route;

Route::delete('projects/{project}', DeleteProjectController::class)
    ->middleware(['auth:web']);
