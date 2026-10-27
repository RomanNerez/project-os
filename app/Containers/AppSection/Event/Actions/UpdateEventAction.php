<?php

namespace App\Containers\AppSection\Event\Actions;

use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\Notifications\EventInvitationNotification;
use App\Containers\AppSection\Event\Tasks\PrepareEventDataTask;
use App\Containers\AppSection\Event\Tasks\UpdateEventTask;
use App\Containers\AppSection\Event\UI\WEB\Requests\UpdateEventRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

final class UpdateEventAction extends ParentAction
{
    public function __construct(
        private readonly PrepareEventDataTask $prepareEventDataTask,
        private readonly UpdateEventTask $updateEventTask,
    ) {
    }

    /**
     * Only newly added participants get an invitation.
     *
     * @throws ValidationException
     */
    public function run(UpdateEventRequest $request, Event $event, User $user): Event
    {
        [$attributes, $participantIds] = $this->prepareEventDataTask->run($request->validated(), $user->id, $event->user_id);

        $invitedIds = DB::transaction(fn (): array => $this->updateEventTask->run($event, $attributes, $participantIds));

        Notification::send(
            User::query()->whereKey($invitedIds)->get(),
            new EventInvitationNotification($event, $user->name),
        );

        return $event;
    }
}
