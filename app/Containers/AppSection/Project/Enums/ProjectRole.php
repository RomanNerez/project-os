<?php

namespace App\Containers\AppSection\Project\Enums;

enum ProjectRole: string
{
    case ADMIN = 'admin';
    case EXECUTOR = 'executor';
    case CLIENT = 'client';
}