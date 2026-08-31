<?php

namespace App\Containers\Integration\AiAgent\Contracts;

use LLPhant\Chat\FunctionInfo\FunctionInfo;

interface AgentToolInterface
{
    public function toFunctionInfo(): FunctionInfo;
}