<?php

use App\Containers\AppSection\Event\UI\WEB\Controllers\DeleteEventController;
use Illuminate\Support\Facades\Route;

Route::delete('calendar/events/{event}', DeleteEventController::class)
    ->name('calendar.events.destroy')
    ->middleware(['auth:web']);
