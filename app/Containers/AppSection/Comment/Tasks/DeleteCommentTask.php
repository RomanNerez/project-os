<?php

namespace App\Containers\AppSection\Comment\Tasks;

use App\Containers\AppSection\Comment\Data\Repositories\CommentRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class DeleteCommentTask extends ParentTask
{
    public function __construct(
        private readonly CommentRepository $repository,
    ) {}

    /**
     * @param int $id
     * @return bool
     */
    public function run(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
