<?php

namespace App\Containers\AppSection\Comment\Tasks;

use App\Containers\AppSection\Comment\Data\Repositories\CommentRepository;
use App\Containers\AppSection\Comment\Models\Comment;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class CreateCommentTask extends ParentTask
{
    public function __construct(
        private readonly CommentRepository $repository,
    ) {}

    /**
     * @param array<string, mixed> $data
     * @return Comment
     */
    public function run(array $data): Comment
    {
        return $this->repository->create($data);
    }
}
