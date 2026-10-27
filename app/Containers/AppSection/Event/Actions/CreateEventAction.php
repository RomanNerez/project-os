<?php

namespace App\Containers\AppSection\Event\Actions;

use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\Notifications\EventInvitationNotification;
use App\Containers\AppSection\Event\Tasks\CreateEventTask;
use App\Containers\AppSection\Event\Tasks\PrepareEventDataTask;
use App\Containers\AppSection\Event\UI\WEB\Requests\CreateEventRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

final class CreateEventAction extends ParentAction
{
    public function __construct(
        private readonly PrepareEventDataTask $prepareEventDataTask,
        private readonly CreateEventTask $createEventTask,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function run(CreateEventRequest $request, User $user): Event
    {
        [$attributes, $participantIds] = $this->prepareEventDataTask->run($request->validated(), $user->id, $user->id);

        $event = DB::transaction(fn (): Event => $this->createEventTask->run(['user_id' => $user->id, ...$attributes], $participantIds));

        Notification::send(
            User::query()->whereKey($participantIds)->get(),
            new EventInvitationNotification($event, $user->name),
        );

        return $event;
    }
}
