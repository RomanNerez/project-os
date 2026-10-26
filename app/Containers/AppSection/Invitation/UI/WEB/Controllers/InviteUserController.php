<?php

namespace App\Containers\AppSection\Invitation\UI\WEB\Controllers;

use App\Containers\AppSection\Invitation\Actions\InviteUserAction;
use App\Containers\AppSection\Invitation\UI\WEB\Requests\InviteUserRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class InviteUserController extends WebController
{
    public function __construct(
        private readonly InviteUserAction $action,
    ) {
    }

    public function __invoke(InviteUserRequest $request): RedirectResponse
    {
        $this->action->run($request->string('email')->toString(), $request->user());

        return back();
    }
}
