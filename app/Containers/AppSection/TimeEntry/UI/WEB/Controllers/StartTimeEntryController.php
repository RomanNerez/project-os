<?php

namespace App\Containers\AppSection\TimeEntry\UI\WEB\Controllers;

use App\Containers\AppSection\TimeEntry\Actions\StartTimeEntryAction;
use App\Containers\AppSection\TimeEntry\UI\WEB\Requests\StartTimeEntryRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class StartTimeEntryController extends WebController
{
    public function __construct(
        private readonly StartTimeEntryAction $action,
    ) {
    }

    /**
     * @param StartTimeEntryRequest $request
     * @return RedirectResponse
     */
    public function __invoke(StartTimeEntryRequest $request): RedirectResponse
    {
        $this->action->run($request, $request->user('web')->id);

        return back();
    }
}
