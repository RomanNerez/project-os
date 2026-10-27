<?php

namespace App\Containers\AppSection\Event\Actions;

use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\Tasks\DeleteEventTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class DeleteEventAction extends ParentAction
{
    public function __construct(
        private readonly DeleteEventTask $deleteEventTask,
    ) {
    }

    public function run(Event $event): bool
    {
        return $this->deleteEventTask->run($event->id);
    }
}
