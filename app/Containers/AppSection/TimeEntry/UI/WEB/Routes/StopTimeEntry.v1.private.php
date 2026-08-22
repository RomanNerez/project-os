<?php

use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\StopTimeEntryController;
use Illuminate\Support\Facades\Route;

Route::post('time-tracker/stop', StopTimeEntryController::class)
    ->middleware(['auth:web']);
