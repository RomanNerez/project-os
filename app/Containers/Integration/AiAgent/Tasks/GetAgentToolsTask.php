<?php

namespace App\Containers\Integration\AiAgent\Tasks;

use App\Containers\Integration\AiAgent\Tools\BaseTool;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class GetAgentToolsTask extends ParentTask
{
    /**
     * @var string
     */
    private string $configKey = 'integration-aiAgent';

    /**
     * @param null|int $userId
     * @return array<int, BaseTool>
     */
    public function run(?int $userId = null): array
    {
        /** @var array<int, BaseTool> $toolClasses */
        $toolClasses = config("{$this->configKey}.tools", []);

        $functions = [];

        foreach ($toolClasses as $toolClass) {
            $tool = app($toolClass)->setUserId($userId);

            $functions[] = $tool->toFunctionInfo();
        }

        return $functions;
    }
}