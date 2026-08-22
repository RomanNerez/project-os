<?php

use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\StartTimeEntryController;
use Illuminate\Support\Facades\Route;

Route::post('time-tracker/start', StartTimeEntryController::class)
    ->middleware(['auth:web']);
