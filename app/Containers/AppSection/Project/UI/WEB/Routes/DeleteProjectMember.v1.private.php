<?php

use App\Containers\AppSection\Project\UI\WEB\Controllers\DeleteProjectMemberController;
use Illuminate\Support\Facades\Route;

Route::delete('projects/{project}/member/{member}', DeleteProjectMemberController::class)
    ->middleware(['auth:web'])
    ->name('projects.memeber.delete');

