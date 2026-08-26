<?php

namespace App\Containers\AppSection\Media\Tasks;

use App\Containers\AppSection\Media\Data\Repositories\MediaRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class DeleteMediaTask extends ParentTask
{
    public function __construct(
        private readonly MediaRepository $repository,
    ) {
    }

    /**
     * @param int $id
     * @return bool
     */
    public function run(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
