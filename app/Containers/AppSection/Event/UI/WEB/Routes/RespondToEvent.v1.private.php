<?php

use App\Containers\AppSection\Event\UI\WEB\Controllers\RespondToEventController;
use Illuminate\Support\Facades\Route;

Route::patch('calendar/events/{event}/respond', RespondToEventController::class)
    ->name('calendar.events.respond')
    ->middleware(['auth:web']);
