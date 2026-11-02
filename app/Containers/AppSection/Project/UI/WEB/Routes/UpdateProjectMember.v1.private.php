<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\UpdateProjectMemberController;
use Illuminate\Support\Facades\Route;

Route::patch('projects/{project}/member/{member}', UpdateProjectMemberController::class)
    ->middleware(['auth:web'])
    ->name('projects.memeber.update');
