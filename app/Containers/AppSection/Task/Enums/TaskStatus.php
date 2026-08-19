<?php

namespace App\Containers\AppSection\Task\Enums;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case IN_REVIEW = 'in_review';
    case DONE = 'done';
    case CANCELLED = 'cancelled';
}
