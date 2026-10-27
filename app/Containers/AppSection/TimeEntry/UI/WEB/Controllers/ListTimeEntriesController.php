<?php

namespace App\Containers\AppSection\TimeEntry\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\FindProjectByIdAction;
use App\Containers\AppSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\AppSection\TimeEntry\Actions\FindRunningTimeEntryAction;
use App\Containers\AppSection\TimeEntry\Actions\ListTimeEntriesAction;
use App\Containers\AppSection\TimeEntry\UI\API\Transformers\TimeEntryTransformer;
use App\Containers\AppSection\TimeEntry\UI\WEB\Requests\ListTimeEntriesRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListTimeEntriesController extends WebController
{
    public function __construct(
        private readonly ListTimeEntriesAction $action,
        private readonly FindRunningTimeEntryAction $findRunningTimeEntryAction,
        private readonly FindProjectByIdAction $findProjectByIdAction,
    ) {
    }

    /**
     * The running timer is global for the user, so it is shown even when it tracks another project.
     *
     * @param ListTimeEntriesRequest $request
     * @param int $id
     * @return Response
     */
    public function __invoke(ListTimeEntriesRequest $request, int $id): Response
    {
        $userId = $request->user('web')->id;
        $project = $this->findProjectByIdAction->run($id);
        $running = $this->findRunningTimeEntryAction->run($userId);

        return Inertia::render('time-tracker', [
            'project' => fractal($project, new ProjectTransformer())->toArray(),
            'entries' => fractal($this->action->run($userId, $project->id), new TimeEntryTransformer())->toArray(),
            'running' => $running ? fractal($running, new TimeEntryTransformer())->toArray() : null,
            'serverTime' => now()->toIso8601String(),
        ]);
    }
}
