<?php

use App\Containers\AppSection\Media\UI\WEB\Controllers\DeleteMediaController;
use Illuminate\Support\Facades\Route;

Route::delete('media/{id}', DeleteMediaController::class)
    ->middleware(['auth:web']);
