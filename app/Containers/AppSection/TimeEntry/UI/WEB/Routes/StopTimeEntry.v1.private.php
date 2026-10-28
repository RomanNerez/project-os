<?php

use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\StopTimeEntryController;
use Illuminate\Support\Facades\Route;

Route::post('projects/{project}/time-tracker/stop', StopTimeEntryController::class)
    ->name('projects.time-tracker.stop')
    ->middleware(['auth:web']);
