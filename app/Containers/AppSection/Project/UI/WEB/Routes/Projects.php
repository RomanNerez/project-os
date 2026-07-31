<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\ProjectsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:web')->group(static function (): void {
    Route::get('projects', [ProjectsController::class, 'showList'])
        ->name('projects.list');
});
