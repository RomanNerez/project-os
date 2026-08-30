<?php

namespace App\Containers\Integration\AiAgent\Actions;

use App\Containers\Integration\AiAgent\Models\AiChatMessage;
use App\Containers\Integration\AiAgent\Tasks\CreateAiChatMessageTask;
use App\Containers\Integration\AiAgent\UI\WEB\Requests\SendChatMessageRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use LLPhant\Chat\Enums\ChatRole;
use LLPhant\Chat\Message;
use LLPhant\Chat\OpenAIChat;
use LLPhant\GeminiOpenAIConfig;

final class SendChatMessageAction extends ParentAction
{
    public function __construct(
        private readonly CreateAiChatMessageTask $createAiChatMessageTask
    ) {}

    /**
     * @param SendChatMessageRequest $request
     * @return string
     */
    public function run(SendChatMessageRequest $request): string
    {
        $model = 'gemini-3.6-flash';
        $user = $request->user();

        $config = new GeminiOpenAIConfig();
        $config->apiKey = env('GEMINI_API_KEY');
        $config->model = $model;
        $chat = new OpenAIChat($config);

        $historyDbMessages = AiChatMessage::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->take(10)
            ->get()
            ->reverse();

        $this->createAiChatMessageTask->run([
            'user_id' => $user->id,
            'role' => ChatRole::User,
            'content' => $request->message,
            'model_name' => $model,
        ]);

        $messages = [
            Message::system('Ти — AI-консультант менежменту про проєктам, та задачам для цього проєкта. Відповідай чітко та коротко.'),
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
            'model_name' => $model,
            'tokens_used' => $chat->getTotalTokens(),
        ]);

        return $response;
    }
}
