<?php

use App\Containers\AppSection\Event\UI\WEB\Controllers\CreateEventController;
use Illuminate\Support\Facades\Route;

Route::post('calendar/events', CreateEventController::class)
    ->name('calendar.events.store')
    ->middleware(['auth:web']);
