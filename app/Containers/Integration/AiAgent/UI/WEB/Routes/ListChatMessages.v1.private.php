<?php

use App\Containers\Integration\AiAgent\UI\WEB\Controllers\ListChatMessagesController;
use Illuminate\Support\Facades\Route;

Route::get('ai-agents/messages', ListChatMessagesController::class)
    ->middleware(['auth:web']);

