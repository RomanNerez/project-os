<?php

namespace App\Containers\AppSection\Project\Enums;

enum ProjectRole: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';
    case VIEWER = 'viewer';
}