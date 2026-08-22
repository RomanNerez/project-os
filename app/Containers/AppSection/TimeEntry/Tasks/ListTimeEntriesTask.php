<?php

namespace App\Containers\AppSection\TimeEntry\Tasks;

use App\Containers\AppSection\TimeEntry\Data\Repositories\TimeEntryRepository;
use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class ListTimeEntriesTask extends ParentTask
{
    public function __construct(
        private readonly TimeEntryRepository $repository,
    ) {
    }

    /**
     * @return Collection<int, TimeEntry>
     */
    public function run(int $userId, int $limit = 100): Collection
    {
        return $this->repository
            ->with(['project'])
            ->scopeQuery(static fn (Builder $query): Builder => $query
                ->where('user_id', $userId)
                ->whereNotNull('stopped_at')
                ->orderByDesc('started_at')
                ->limit($limit))
            ->all();
    }
}
