<?php

namespace App\Containers\AppSection\Event\Actions;

use App\Containers\AppSection\Event\Enums\EventParticipantStatus;
use App\Containers\AppSection\Event\Models\Event;
use App\Ship\Parents\Actions\Action as ParentAction;

final class RespondToEventAction extends ParentAction
{
    public function run(Event $event, int $userId, EventParticipantStatus $status): void
    {
        $event->participants()->updateExistingPivot($userId, ['status' => $status->value]);
    }
}
