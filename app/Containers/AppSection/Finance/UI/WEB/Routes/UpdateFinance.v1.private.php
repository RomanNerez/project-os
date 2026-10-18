<?php

use App\Containers\AppSection\Finance\UI\WEB\Controllers\UpdateFinanceController;
use Illuminate\Support\Facades\Route;

Route::put('finances/{finance}', UpdateFinanceController::class)
    ->name('finances.update')
    ->middleware(['auth:web']);
