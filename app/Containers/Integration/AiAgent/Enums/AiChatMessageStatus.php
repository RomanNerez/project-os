<?php

namespace App\Containers\Integration\AiAgent\Enums;

enum AiChatMessageStatus: string
{
    case COMPLETED = 'completed';
    case PENDING = 'pending';
    case FAILED = 'failed';
}