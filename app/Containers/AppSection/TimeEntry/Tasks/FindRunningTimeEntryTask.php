<?php

namespace App\Containers\AppSection\TimeEntry\Tasks;

use App\Containers\AppSection\TimeEntry\Data\Repositories\TimeEntryRepository;
use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Database\Eloquent\Builder;

final class FindRunningTimeEntryTask extends ParentTask
{
    public function __construct(
        private readonly TimeEntryRepository $repository,
    ) {
    }

    public function run(int $userId): ?TimeEntry
    {
        return $this->repository
            ->with(['project'])
            ->scopeQuery(static fn (Builder $query): Builder => $query
                ->where('user_id', $userId)
                ->whereNull('stopped_at')
                ->latest('started_at'))
            ->first();
    }
}
