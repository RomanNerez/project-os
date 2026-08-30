<?php

namespace App\Containers\Integration\AiAgent\UI\WEB\Controllers;

use App\Containers\Integration\AiAgent\Actions\SendChatMessageAction;
use App\Containers\Integration\AiAgent\UI\WEB\Requests\SendChatMessageRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class SendChatMessageController extends WebController
{
    public function __construct(
        private readonly SendChatMessageAction $action
    ) {}

    /**
     * 
     */
    public function __invoke(SendChatMessageRequest $request): RedirectResponse
    {
        $this->action->run($request);

        return back();
    }
}
