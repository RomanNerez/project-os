<?php

namespace App\Containers\AppSection\TimeEntry\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\TimeEntry\Actions\StopTimeEntryAction;
use App\Containers\AppSection\TimeEntry\UI\WEB\Requests\StopTimeEntryRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class StopTimeEntryController extends WebController
{
    public function __construct(
        private readonly StopTimeEntryAction $action,
    ) {
    }

    /**
     * @param StopTimeEntryRequest $request
     * @param Project $project
     * @return RedirectResponse
     */
    public function __invoke(StopTimeEntryRequest $request, Project $project): RedirectResponse
    {
        $this->action->run($request->user('web')->id);

        return back();
    }
}
