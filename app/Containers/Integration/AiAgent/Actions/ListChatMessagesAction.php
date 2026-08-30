<?php

namespace App\Containers\Integration\AiAgent\Actions;

use App\Containers\Integration\AiAgent\Tasks\ListChatMessagesTask;
use App\Containers\Integration\AiAgent\UI\WEB\Requests\ListChatMessagesRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class ListChatMessagesAction extends ParentAction
{
    public function __construct(
        private readonly ListChatMessagesTask $listChatMessagesTask
    ) {}

    /**
     * @param ListChatMessagesRequest $request
     * @return mixed
     */
    public function run(ListChatMessagesRequest $request): mixed
    {
        return $this->listChatMessagesTask->run();
    }
}
