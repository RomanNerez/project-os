<?php

namespace App\Containers\AppSection\Event\UI\WEB\Controllers;

use App\Containers\AppSection\Event\Actions\DeleteEventAction;
use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\UI\WEB\Requests\DeleteEventRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class DeleteEventController extends WebController
{
    public function __construct(
        private readonly DeleteEventAction $action,
    ) {
    }

    public function __invoke(DeleteEventRequest $request, Event $event): RedirectResponse
    {
        $this->action->run($event);

        return back();
    }
}
