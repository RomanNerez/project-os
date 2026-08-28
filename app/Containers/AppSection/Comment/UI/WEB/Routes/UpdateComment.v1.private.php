<?php

use App\Containers\AppSection\Comment\UI\WEB\Controllers\UpdateCommentController;
use Illuminate\Support\Facades\Route;

Route::patch('comment/{id}', UpdateCommentController::class)
    ->middleware(['auth:web']);