<?php

use App\Containers\AppSection\Finance\UI\WEB\Controllers\CreateFinanceController;
use Illuminate\Support\Facades\Route;

Route::post('projects/{project}/finances', CreateFinanceController::class)
    ->middleware(['auth:web'])
    ->name('projects.finances.store');
