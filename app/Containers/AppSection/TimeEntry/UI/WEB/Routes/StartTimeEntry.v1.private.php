<?php

use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\StartTimeEntryController;
use Illuminate\Support\Facades\Route;

Route::post('projects/{project}/time-tracker/start', StartTimeEntryController::class)
    ->name('projects.time-tracker.start')
    ->middleware(['auth:web']);
