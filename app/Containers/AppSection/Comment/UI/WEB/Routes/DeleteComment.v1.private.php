<?php

use App\Containers\AppSection\Comment\UI\WEB\Controllers\DeleteCommentController;
use Illuminate\Support\Facades\Route;

Route::delete('comment/{id}', DeleteCommentController::class)
    ->middleware(['auth:web']);