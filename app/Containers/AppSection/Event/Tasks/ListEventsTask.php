<?php

namespace App\Containers\AppSection\Event\Tasks;

use App\Containers\AppSection\Event\Data\Repositories\EventRepository;
use App\Containers\AppSection\Event\Models\Event;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final class ListEventsTask extends ParentTask
{
    public function __construct(
        private readonly EventRepository $repository,
    ) {
    }

    /**
     * @return Collection<int, Event>
     */
    public function run(int $userId, CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return $this->repository
            ->addRequestCriteria()
            ->with(['project.members', 'participants.media'])
            ->scopeVisibleForUserBetween($userId, $start, $end)
            ->orderBy('start_at')
            ->all();
    }
}
