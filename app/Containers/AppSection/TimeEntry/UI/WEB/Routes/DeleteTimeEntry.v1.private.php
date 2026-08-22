<?php

use App\Containers\AppSection\TimeEntry\UI\WEB\Controllers\DeleteTimeEntryController;
use Illuminate\Support\Facades\Route;

Route::delete('time-tracker/entries/{id}', DeleteTimeEntryController::class)
    ->middleware(['auth:web']);
