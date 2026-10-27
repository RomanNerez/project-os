<?php

use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\ListTimeEntriesController;
use Illuminate\Support\Facades\Route;

Route::get('projects/{id}/time-tracker', ListTimeEntriesController::class)
    ->name('projects.time-tracker.index')
    ->middleware(['auth:web']);
