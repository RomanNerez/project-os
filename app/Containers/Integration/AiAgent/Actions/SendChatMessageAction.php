<?php

namespace App\Containers\Integration\AiAgent\Actions;

use App\Containers\Integration\AiAgent\Enums\AiChatMessageStatus;
use App\Containers\Integration\AiAgent\Jobs\RunAiAgentJob;
use App\Containers\Integration\AiAgent\Tasks\CreateAiChatMessageTask;
use App\Containers\Integration\AiAgent\UI\WEB\Requests\SendChatMessageRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use LLPhant\Chat\Enums\ChatRole;

final class SendChatMessageAction extends ParentAction
{
    public function __construct(
        private readonly CreateAiChatMessageTask $createAiChatMessageTask
    ) {}

    /**
     * @param SendChatMessageRequest $request
     * @return void
     */
    public function run(SendChatMessageRequest $request): void
    {
        $user = $request->user();

        $this->createAiChatMessageTask->run([
            'user_id' => $user->id,
            'role' => ChatRole::User,
            'content' => $request->message,
        ]);

        $aiChatMessage = $this->createAiChatMessageTask->run([
            'user_id' => $user->id,
            'role' => ChatRole::Assistant,
            'status' => AiChatMessageStatus::PENDING,
            'content' => '',
        ]);

        RunAiAgentJob::dispatch($aiChatMessage->id);
    }
}
