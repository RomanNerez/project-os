<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\AddProjectMemberController;
use Illuminate\Support\Facades\Route;

Route::post('projects/{project}/member', AddProjectMemberController::class)
    ->middleware(['auth:web'])
    ->name('projects.memeber');

