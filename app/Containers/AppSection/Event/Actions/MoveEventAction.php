<?php

namespace App\Containers\AppSection\Event\Actions;

use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\Tasks\PrepareEventDataTask;
use App\Containers\AppSection\Event\Tasks\UpdateEventTask;
use App\Containers\AppSection\Event\UI\WEB\Requests\MoveEventRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class MoveEventAction extends ParentAction
{
    public function __construct(
        private readonly UpdateEventTask $updateEventTask,
    ) {
    }

    /**
     * Drag & drop or resize in the calendar: only the period changes.
     */
    public function run(MoveEventRequest $request, Event $event): Event
    {
        $this->updateEventTask->run($event, PrepareEventDataTask::period($request->validated()));

        return $event;
    }
}
