<?php

namespace App\Containers\AppSection\Invitation\UI\WEB\Controllers;

use App\Containers\AppSection\Invitation\Actions\ResendInvitationAction;
use App\Containers\AppSection\Invitation\UI\WEB\Requests\ResendInvitationRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class ResendInvitationController extends WebController
{
    public function __construct(
        private readonly ResendInvitationAction $action,
    ) {
    }

    public function __invoke(ResendInvitationRequest $request, int $id): RedirectResponse
    {
        $this->action->run($id, $request->user());

        return back();
    }
}
