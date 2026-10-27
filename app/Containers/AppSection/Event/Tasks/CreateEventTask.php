<?php

namespace App\Containers\AppSection\Event\Tasks;

use App\Containers\AppSection\Event\Data\Repositories\EventRepository;
use App\Containers\AppSection\Event\Models\Event;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class CreateEventTask extends ParentTask
{
    public function __construct(
        private readonly EventRepository $repository,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int, int> $participantIds
     */
    public function run(array $data, array $participantIds): Event
    {
        $event = $this->repository->create($data);
        $event->participants()->sync($participantIds);

        return $event;
    }
}
