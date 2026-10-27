<?php

use App\Containers\AppSection\Event\UI\WEB\Controllers\ListEventParticipantCandidatesController;
use Illuminate\Support\Facades\Route;

Route::get('calendar/participants', ListEventParticipantCandidatesController::class)
    ->name('calendar.participants.index')
    ->middleware(['auth:web']);
