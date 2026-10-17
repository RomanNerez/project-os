<?php

use App\Containers\AppSection\Finance\UI\WEB\Controllers\ListFinancesController;
use Illuminate\Support\Facades\Route;

Route::get('finances', ListFinancesController::class)
    ->name('finances.index')
    ->middleware(['auth:web']);
