<?php

use App\Containers\AppSection\Finance\UI\WEB\Controllers\ListFinancesController;
use Illuminate\Support\Facades\Route;

Route::get('projects/{id}/finances', ListFinancesController::class)
    ->name('projects.finances.index')
    ->middleware(['auth:web']);
