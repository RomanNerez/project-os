<?php

namespace App\Containers\AppSection\TimeEntry\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\ListAllProjectsAction;
use App\Containers\AppSection\Project\Models\Project;
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
        private readonly ListAllProjectsAction $listAllProjectsAction,
    ) {
    }

    /**
     * @param ListTimeEntriesRequest $request
     * @return Response
     */
    public function __invoke(ListTimeEntriesRequest $request): Response
    {
        $userId = $request->user('web')->id;
        $running = $this->findRunningTimeEntryAction->run($userId);

        return Inertia::render('time-tracker', [
            'entries' => fractal($this->action->run($userId), new TimeEntryTransformer())->toArray(),
            'running' => $running ? fractal($running, new TimeEntryTransformer())->toArray() : null,
            'projects' => $this->listAllProjectsAction->run()
                ->map(static fn (Project $project): array => [
                    'id' => $project->id,
                    'title' => $project->title,
                ])->values(),
            'serverTime' => now()->toIso8601String(),
        ]);
    }
}
