<?php

use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\ListTimeEntriesController;
use Illuminate\Support\Facades\Route;

Route::get('time-tracker', ListTimeEntriesController::class)
    ->name('time-tracker.index')
    ->middleware(['auth:web']);
