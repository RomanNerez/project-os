<?php

use App\Containers\AppSection\Event\UI\WEB\Controllers\ShowCalendarController;
use Illuminate\Support\Facades\Route;

Route::get('calendar', ShowCalendarController::class)
    ->name('calendar.index')
    ->middleware(['auth:web']);
