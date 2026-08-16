<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\DeleteProjectController;
use Illuminate\Support\Facades\Route;

Route::delete('projects/{id}', DeleteProjectController::class)
    ->middleware(['auth:web']);
