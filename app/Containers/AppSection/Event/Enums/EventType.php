<?php

namespace App\Containers\AppSection\Event\Enums;

enum EventType: string
{
    case MEETING = 'meeting';
    case DEADLINE = 'deadline';
    case REMINDER = 'reminder';
    case PERSONAL = 'personal';
    case OTHER = 'other';
}
