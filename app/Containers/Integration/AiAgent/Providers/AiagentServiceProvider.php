<?php

namespace App\Containers\Integration\AiAgent\Providers;

use App\Containers\Integration\AiAgent\Tasks\ListChatMessagesTask;
use App\Containers\Integration\AiAgent\UI\API\Transformers\AiChatMessageTransformer;
use App\Ship\Parents\Providers\ServiceProvider as ParentServiceProvider;
use Inertia\Inertia;

final class AiagentServiceProvider extends ParentServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        Inertia::share('ai_agent', [
            'messages' => fractal(app(ListChatMessagesTask::class)->run(), new AiChatMessageTransformer())
        ]);
    }

    /**
     * @return void
     */
    public function register(): void
    {
        // 
    }
}
