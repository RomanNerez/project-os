<?php

namespace App\Containers\AppSection\Event\Tasks;

use App\Containers\AppSection\Event\Data\Repositories\EventRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class DeleteEventTask extends ParentTask
{
    public function __construct(
        private readonly EventRepository $repository,
    ) {
    }

    public function run(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
