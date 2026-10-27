<?php

namespace App\Containers\AppSection\Event\Enums;

enum EventParticipantStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
}
