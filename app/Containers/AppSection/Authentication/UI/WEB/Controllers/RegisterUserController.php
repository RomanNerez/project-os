<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Controllers;

use App\Containers\AppSection\Authentication\Actions\RegisterUserAction;
use App\Containers\AppSection\Authentication\UI\WEB\Requests\RegisterUserRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class RegisterUserController extends WebController
{
    public function __construct(
        private readonly RegisterUserAction $action
    ) {}

    /**
     * @param RegisterUserRequest $request
     * @return RedirectResponse
     */
    public function __invoke(RegisterUserRequest $request): RedirectResponse
    {
        $this->action->run($request);

        return redirect('/');
    }
}
