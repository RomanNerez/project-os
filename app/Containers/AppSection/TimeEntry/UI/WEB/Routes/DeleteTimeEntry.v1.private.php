<?php

use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\DeleteTimeEntryController;
use Illuminate\Support\Facades\Route;

Route::delete('projects/{project}/time-tracker/entries/{id}', DeleteTimeEntryController::class)
    ->name('projects.time-tracker.destroy')
    ->middleware(['auth:web']);
