<?php

namespace App\Containers\Integration\AiAgent\Tasks;

use App\Containers\Integration\AiAgent\Data\Repositories\AiChatMessageRepository;
use App\Containers\Integration\AiAgent\Models\AiChatMessage;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class CreateAiChatMessageTask extends ParentTask
{
    public function __construct(
        private readonly AiChatMessageRepository $repository
    ) {}

    /**
     * @param array<string, mixed> $data
     * @return AiChatMessage
     */
    public function run(array $data): AiChatMessage
    {
        return $this->repository->create($data);
    }
}