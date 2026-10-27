<?php

namespace App\Containers\AppSection\Event\UI\WEB\Controllers;

use App\Containers\AppSection\Event\Actions\MoveEventAction;
use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\UI\WEB\Requests\MoveEventRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class MoveEventController extends WebController
{
    public function __construct(
        private readonly MoveEventAction $action,
    ) {
    }

    public function __invoke(MoveEventRequest $request, Event $event): RedirectResponse
    {
        $this->action->run($request, $event);

        return back();
    }
}
