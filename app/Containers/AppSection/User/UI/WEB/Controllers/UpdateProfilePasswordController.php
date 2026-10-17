<?php

namespace App\Containers\AppSection\User\UI\WEB\Controllers;

use App\Containers\AppSection\User\Actions\UpdatePasswordAction;
use App\Containers\AppSection\User\UI\WEB\Requests\UpdateProfilePasswordRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UpdateProfilePasswordController extends WebController
{
    public function __construct(
        private readonly UpdatePasswordAction $action,
    ) {
    }

    public function __invoke(UpdateProfilePasswordRequest $request): RedirectResponse
    {
        $this->action->run($request->user('web')->id, $request->string('new_password')->toString());

        return back();
    }
}
