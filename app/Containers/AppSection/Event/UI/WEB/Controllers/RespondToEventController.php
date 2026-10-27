<?php

namespace App\Containers\AppSection\Event\UI\WEB\Controllers;

use App\Containers\AppSection\Event\Actions\RespondToEventAction;
use App\Containers\AppSection\Event\Enums\EventParticipantStatus;
use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\UI\WEB\Requests\RespondToEventRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class RespondToEventController extends WebController
{
    public function __construct(
        private readonly RespondToEventAction $action,
    ) {
    }

    public function __invoke(RespondToEventRequest $request, Event $event): RedirectResponse
    {
        $this->action->run($event, $request->user('web')->id, $request->enum('status', EventParticipantStatus::class));

        return back();
    }
}
