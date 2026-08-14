<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\StoreProjectController;
use Illuminate\Support\Facades\Route;

Route::put('projects/{id}', StoreProjectController::class)
    ->middleware(['auth:web']);

