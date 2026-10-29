<?php

use App\Containers\AppSection\Media\UI\WEB\Controllers\DeleteProjectTaskMediaController;
use Illuminate\Support\Facades\Route;

Route::delete('projects/{project}/tasks/{task}/media/{media}', DeleteProjectTaskMediaController::class)
    ->middleware(['auth:web'])
    ->scopeBindings()
    ->name('projects.tasks.media.destroy');
