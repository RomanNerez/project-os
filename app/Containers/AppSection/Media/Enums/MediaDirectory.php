<?php

namespace App\Containers\AppSection\Media\Enums;

/**
 * Fixed directory names used by the media path generators.
 */
enum MediaDirectory: string
{
    case PROJECTS = 'projects';
    case TASKS = 'tasks';
    case FINANCE = 'finance';
    case USERS = 'users';
}
