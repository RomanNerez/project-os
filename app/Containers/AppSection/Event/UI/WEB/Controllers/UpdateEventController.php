<?php

namespace App\Containers\AppSection\Event\UI\WEB\Controllers;

use App\Containers\AppSection\Event\Actions\UpdateEventAction;
use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\UI\WEB\Requests\UpdateEventRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UpdateEventController extends WebController
{
    public function __construct(
        private readonly UpdateEventAction $action,
    ) {
    }

    public function __invoke(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $this->action->run($request, $event, $request->user('web'));

        return back();
    }
}
