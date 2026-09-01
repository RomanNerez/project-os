<?php

namespace App\Containers\Integration\AiAgent\Tasks;

use App\Containers\Integration\AiAgent\Data\Repositories\AiChatMessageRepository;
use App\Containers\Integration\AiAgent\Models\AiChatMessage;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class FindAiChatMessageByIdTask extends ParentTask
{
    public function __construct(
        private readonly AiChatMessageRepository $repository
    ) {}

    /**
     * @param int $id
     * @return AiChatMessage
     */
    public function run(int $id): AiChatMessage
    {
        return $this->repository->findOrFail($id);
    }
}