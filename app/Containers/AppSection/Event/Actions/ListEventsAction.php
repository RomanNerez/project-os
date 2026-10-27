<?php

namespace App\Containers\AppSection\Event\Actions;

use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Event\Tasks\ListEventsTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final class ListEventsAction extends ParentAction
{
    public function __construct(
        private readonly ListEventsTask $listEventsTask,
    ) {
    }

    /**
     * @return Collection<int, Event>
     */
    public function run(int $userId, CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return $this->listEventsTask->run($userId, $start, $end);
    }
}
