<?php

use App\Containers\AppSection\Notification\UI\WEB\Controllers\ListNotificationsController;
use Illuminate\Support\Facades\Route;

Route::get('notifications', ListNotificationsController::class)
    ->name('notifications.index')
    ->middleware(['auth:web']);
