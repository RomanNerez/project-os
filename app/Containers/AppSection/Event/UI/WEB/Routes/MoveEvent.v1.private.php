<?php

use App\Containers\AppSection\Event\UI\WEB\Controllers\MoveEventController;
use Illuminate\Support\Facades\Route;

Route::patch('calendar/events/{event}/move', MoveEventController::class)
    ->name('calendar.events.move')
    ->middleware(['auth:web']);
