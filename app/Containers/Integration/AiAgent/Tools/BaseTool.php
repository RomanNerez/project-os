<?php

namespace App\Containers\Integration\AiAgent\Tools;

use App\Containers\Integration\AiAgent\Contracts\AgentToolInterface;

abstract class BaseTool implements AgentToolInterface
{
    /**
     * @var null|int
     */
    protected ?int $userId = null;

    /**
     * @param int $userId
     * @return static
     */
    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }
}