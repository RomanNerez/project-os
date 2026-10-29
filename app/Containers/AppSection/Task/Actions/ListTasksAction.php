<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tasks\ListTasksTask;
use App\Containers\AppSection\Task\UI\WEB\Requests\ListTasksRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListTasksAction extends ParentAction
{
    public function __construct(
        private readonly ListTasksTask $listTasksTask,
    ) {}

    /**
     * @param ListTasksRequest $request
     * @param int|null $projectId
     * @return LengthAwarePaginator<int, Task>
     */
    public function run(ListTasksRequest $request, ?int $projectId = null): LengthAwarePaginator
    {
        $user = $request->user();

        return $this->listTasksTask->run(userId: $user->id, with: [
            'project.user',
            'project.members',
            'assignee',
            'comments' => fn($query) => $query->with('user')->orderBy('created_at', 'desc'),
            'timeEntries' => fn($query) => $query->whereNotNull('stopped_at')->with('user.media')->orderByDesc('started_at'),
            'activitiesAsSubject' => fn($query) => $query->with('causer.media')->latest('id'),
        ], projectId: $projectId);
    }
}
