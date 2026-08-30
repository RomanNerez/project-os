<?php

namespace App\Containers\Integration\AiAgent\UI\WEB\Controllers;

use App\Containers\Integration\AiAgent\Actions\ListChatMessagesAction;
use App\Containers\Integration\AiAgent\UI\WEB\Requests\ListChatMessagesRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class ListChatMessagesController extends WebController
{
    public function __construct(
        private readonly ListChatMessagesAction $action
    ) {}

    /**
     * 
     */
    public function __invoke(ListChatMessagesRequest $request): RedirectResponse
    {
        $this->action->run($request);

        return back();
    }
}
