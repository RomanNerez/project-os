<?php

namespace App\Containers\Integration\AiAgent\Actions;

use App\Containers\Integration\AiAgent\Enums\AiChatMessageStatus;
use App\Containers\Integration\AiAgent\Models\AiChatMessage;
use App\Containers\Integration\AiAgent\Tasks\FindAiChatMessageByIdTask;
use App\Containers\Integration\AiAgent\Tasks\GetAgentToolsTask;
use App\Containers\Integration\AiAgent\Tasks\GetConfigAgentProviderTask;
use App\Containers\Integration\AiAgent\Tasks\UpdateAiChatMessageTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use LLPhant\Chat\Enums\ChatRole;
use LLPhant\Chat\Message;
use LLPhant\Chat\OpenAIChat;

final class GenerateAiMessageAction extends ParentAction
{
    public function __construct(
        private readonly FindAiChatMessageByIdTask  $findAiChatMessageByIdTask,
        private readonly UpdateAiChatMessageTask    $updateAiChatMessageTask,
        private readonly GetConfigAgentProviderTask $getConfigAgentProviderTask,
        private readonly GetAgentToolsTask          $getAgentToolsTask
    ) {}

    /**
     * @param int $aiChatMessageId
     * @return void
     */
    public function run(int $aiChatMessageId): void
    {
        $aiChatMessage = $this->findAiChatMessageByIdTask->run($aiChatMessageId);
        $config = $this->getConfigAgentProviderTask->run();

        $chat = new OpenAIChat($config);
        
        $chat->setTools(
            $this->getAgentToolsTask->run($aiChatMessage->user_id)
        );

        $historyDbMessages = AiChatMessage::query()
            ->where('user_id', $aiChatMessage->user_id)
            ->latest('id')
            ->take(10)
            ->get()
            ->reverse();

        $messages = [
            Message::system('Ти — AI-консультант менежменту про проєктам, та задачам для цього проєкта. Відповідай чітко та коротко. Не використовуй MARKDOWN, використовуй HTML, коли виконуєш якісь фінкції'),
        ];

        foreach ($historyDbMessages as $historyDbMessage) {
            $messages[] = match($historyDbMessage->role) {
                ChatRole::User => Message::user($historyDbMessage->content),
                ChatRole::Assistant => Message::assistant($historyDbMessage->content),
            };
        }

        $response = $chat->generateChat($messages);

        $this->updateAiChatMessageTask->run($aiChatMessageId, [
            'content' => $response,
            'status' => AiChatMessageStatus::COMPLETED,
            'model_name' => $config->model,
            'tokens_used' => $chat->getTotalTokens(),
        ]);
    }
}