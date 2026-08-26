<?php

use App\Containers\AppSection\Task\UI\WEB\Controllers\UploadTaskFilesController;
use Illuminate\Support\Facades\Route;

Route::post('tasks/{id}/files', UploadTaskFilesController::class)
    ->middleware(['auth:web']);
