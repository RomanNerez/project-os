<?php

use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\UpdateTimeEntryController;
use Illuminate\Support\Facades\Route;

Route::patch('projects/{project}/time-tracker/entries/{id}', UpdateTimeEntryController::class)
    ->name('projects.time-tracker.update')
    ->middleware(['auth:web']);
