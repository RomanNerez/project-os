<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Controllers;

use App\Containers\AppSection\Authentication\Actions\RegisterUserAction;
use App\Containers\AppSection\Authentication\UI\WEB\Requests\RegisterUserRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class RegisterUserController extends WebController
{
    public function __construct(
        private readonly RegisterUserAction $action,
    ) {
    }

    public function __invoke(RegisterUserRequest $request): RedirectResponse
    {
        $user = $this->action->run($request);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('projects.index');
    }
}
