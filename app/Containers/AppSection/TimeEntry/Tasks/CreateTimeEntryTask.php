<?php

namespace App\Containers\AppSection\TimeEntry\Tasks;

use App\Containers\AppSection\TimeEntry\Data\Repositories\TimeEntryRepository;
use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class CreateTimeEntryTask extends ParentTask
{
    public function __construct(
        private readonly TimeEntryRepository $repository,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return TimeEntry
     */
    public function run(array $data): TimeEntry
    {
        return $this->repository->create($data);
    }
}
