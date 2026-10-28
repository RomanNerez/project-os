<?php

use App\Containers\AppSection\Finance\UI\WEB\Controllers\UpdateFinanceController;
use Illuminate\Support\Facades\Route;

Route::put('projects/{project}/finances/{finance}', UpdateFinanceController::class)
    ->middleware(['auth:web'])
    ->scopeBindings()
    ->name('projects.finances.update');
