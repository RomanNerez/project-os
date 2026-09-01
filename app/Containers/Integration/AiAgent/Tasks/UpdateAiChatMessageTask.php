<?php

namespace App\Containers\Integration\AiAgent\Tasks;

use App\Containers\Integration\AiAgent\Data\Repositories\AiChatMessageRepository;
use App\Containers\Integration\AiAgent\Models\AiChatMessage;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class UpdateAiChatMessageTask extends ParentTask
{
    public function __construct(
        private readonly AiChatMessageRepository $repository
    ) {}

    /**
     * @param int $id
     * @param array<string, mixed> $attributes
     * @return AiChatMessage
     */
    public function run(int $id, array $attributes): AiChatMessage
    {
        return $this->repository->update($attributes, $id);
    }
}