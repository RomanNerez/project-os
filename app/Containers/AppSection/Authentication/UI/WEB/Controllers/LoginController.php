<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Controllers;

use App\Containers\AppSection\Authentication\Actions\Web\LoginAction;
use App\Containers\AppSection\Authentication\UI\WEB\Requests\LoginRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class LoginController extends WebController
{
    public function __invoke(LoginRequest $request, LoginAction $action): RedirectResponse
    {
        return $action->run(
            $request->input('email'),
            $request->input('password'),
            $request->boolean('remember'),
        );
    }

    public function showForm(): Response
    {
        return Inertia::render('login');
    }
}
