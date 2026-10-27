<?php

use App\Containers\AppSection\Notification\UI\WEB\Controllers\MarkAllNotificationsAsReadController;
use Illuminate\Support\Facades\Route;

Route::patch('notifications/read-all', MarkAllNotificationsAsReadController::class)
    ->name('notifications.read-all')
    ->middleware(['auth:web']);
