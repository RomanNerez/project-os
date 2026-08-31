<?php

namespace App\Containers\Integration\AiAgent\Actions;

use App\Containers\Integration\AiAgent\Models\AiChatMessage;
use App\Containers\Integration\AiAgent\Tasks\CreateAiChatMessageTask;
use App\Containers\Integration\AiAgent\Tasks\GetConfigAgentProviderTask;
use App\Containers\Integration\AiAgent\Tools\CreateProjectTaskTool;
use App\Containers\Integration\AiAgent\UI\WEB\Requests\SendChatMessageRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use LLPhant\Chat\Enums\ChatRole;
use LLPhant\Chat\Message;
use LLPhant\Chat\OpenAIChat;

final class SendChatMessageAction extends ParentAction
{
    public function __construct(
        private readonly GetConfigAgentProviderTask $getConfigAgentProviderTask,
        private readonly CreateAiChatMessageTask $createAiChatMessageTask
    ) {}

    /**
     * @param SendChatMessageRequest $request
     * @return string
     */
    public function run(SendChatMessageRequest $request): string
    {
        $user = $request->user();

        $this->createAiChatMessageTask->run([
            'user_id' => $user->id,
            'role' => ChatRole::User,
            'content' => $request->message,
        ]);

        $config = $this->getConfigAgentProviderTask->run();

        $chat = new OpenAIChat($config);

        $createTaskFunction = app(CreateProjectTaskTool::class)
            ->setUserId($user->id)
            ->toFunctionInfo();
        
        $chat->addTool($createTaskFunction);

        $historyDbMessages = AiChatMessage::query()
            ->where('user_id', $user->id)
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

        $this->createAiChatMessageTask->run([
            'user_id' => $user->id,
            'role' => ChatRole::Assistant,
            'content' => $response,
            'model_name' => $config->model,
            'tokens_used' => $chat->getTotalTokens(),
        ]);

        return $response;
    }
}
