<?php

use App\Containers\AppSection\Notification\UI\WEB\Controllers\MarkNotificationAsReadController;
use Illuminate\Support\Facades\Route;

Route::patch('notifications/{id}/read', MarkNotificationAsReadController::class)
    ->name('notifications.read')
    ->whereUuid('id')
    ->middleware(['auth:web']);
