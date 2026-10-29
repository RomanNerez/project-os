<?php

namespace App\Containers\AppSection\TimeEntry\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\TimeEntry\Actions\UpdateTimeEntryAction;
use App\Containers\AppSection\TimeEntry\UI\WEB\Requests\UpdateTimeEntryRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UpdateTimeEntryController extends WebController
{
    public function __construct(
        private readonly UpdateTimeEntryAction $action,
    ) {
    }

    /**
     * @param UpdateTimeEntryRequest $request
     * @param Project $project
     * @param int $id
     * @return RedirectResponse
     */
    public function __invoke(UpdateTimeEntryRequest $request, Project $project, int $id): RedirectResponse
    {
        $this->action->run($id, $request->user('web')->id, $project->id, $request->validated());

        return back();
    }
}
