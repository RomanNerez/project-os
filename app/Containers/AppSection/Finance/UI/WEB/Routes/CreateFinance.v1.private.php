<?php

use App\Containers\AppSection\Finance\UI\WEB\Controllers\CreateFinanceController;
use Illuminate\Support\Facades\Route;

Route::post('finances', CreateFinanceController::class)
    ->name('finances.store')
    ->middleware(['auth:web']);
