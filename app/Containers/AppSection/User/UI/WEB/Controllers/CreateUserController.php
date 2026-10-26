<?php

namespace App\Containers\AppSection\User\UI\WEB\Controllers;

use App\Containers\AppSection\User\Actions\CreateUserAction;
use App\Containers\AppSection\User\UI\WEB\Requests\CreateUserRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class CreateUserController extends WebController
{
    public function __construct(
        private readonly CreateUserAction $action,
    ) {
    }

    public function __invoke(CreateUserRequest $request): RedirectResponse
    {
        $this->action->run($request->validated());

        return back();
    }
}
