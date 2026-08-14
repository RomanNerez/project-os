<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\StoreProjectController;
use Illuminate\Support\Facades\Route;

Route::post('projects', StoreProjectController::class)
    ->middleware(['auth:web']);

