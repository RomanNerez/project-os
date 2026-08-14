<?php

namespace App\Containers\AppSection\Project\Enums;

enum ProjectStatus: string
{
    case DRAFT = 'draft';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD = 'on_hold';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}