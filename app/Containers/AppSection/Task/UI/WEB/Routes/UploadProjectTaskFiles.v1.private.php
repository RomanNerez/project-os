<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\UploadProjectTaskFilesController;
use Illuminate\Support\Facades\Route;

Route::post('projects/{project}/tasks/{task}/files', UploadProjectTaskFilesController::class)
    ->middleware(['auth:web'])
    ->scopeBindings()
    ->name('projects.tasks.files.store');
