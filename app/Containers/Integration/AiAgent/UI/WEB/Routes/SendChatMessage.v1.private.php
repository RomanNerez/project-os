<?php

use App\Containers\Integration\AiAgent\UI\WEB\Controllers\SendChatMessageController;
use Illuminate\Support\Facades\Route;

Route::post('ai-agents/messages', SendChatMessageController::class)
    ->middleware(['auth:web']);

