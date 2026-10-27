<?php

namespace App\Containers\AppSection\Event\UI\WEB\Controllers;

use App\Containers\AppSection\Event\Actions\CreateEventAction;
use App\Containers\AppSection\Event\UI\WEB\Requests\CreateEventRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class CreateEventController extends WebController
{
    public function __construct(
        private readonly CreateEventAction $action,
    ) {
    }

    public function __invoke(CreateEventRequest $request): RedirectResponse
    {
        $this->action->run($request, $request->user('web'));

        return back();
    }
}
