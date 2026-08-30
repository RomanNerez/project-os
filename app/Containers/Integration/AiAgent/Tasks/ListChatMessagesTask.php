<?php

namespace App\Containers\Integration\AiAgent\Tasks;

use App\Containers\Integration\AiAgent\Data\Repositories\AiChatMessageRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class ListChatMessagesTask extends ParentTask
{
    public function __construct(
        private readonly AiChatMessageRepository $repository
    ) {}

    /**
     * @return mixed
     */
    public function run(): mixed
    {
        return $this->repository->latest()->paginate()->reverse();
    }
}