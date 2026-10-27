<?php

use App\Containers\AppSection\Event\UI\WEB\Controllers\ListEventsController;
use Illuminate\Support\Facades\Route;

Route::get('calendar/events', ListEventsController::class)
    ->name('calendar.events.index')
    ->middleware(['auth:web']);
