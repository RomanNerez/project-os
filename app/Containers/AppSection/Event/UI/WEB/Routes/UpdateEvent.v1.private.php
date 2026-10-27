<?php

use App\Containers\AppSection\Event\UI\WEB\Controllers\UpdateEventController;
use Illuminate\Support\Facades\Route;

Route::put('calendar/events/{event}', UpdateEventController::class)
    ->name('calendar.events.update')
    ->middleware(['auth:web']);
