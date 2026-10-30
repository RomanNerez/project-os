<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Controllers;

use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ShowResetPasswordFormController extends WebController
{
    public function __invoke(Request $request, string $token): Response
    {
        return Inertia::render('reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }
}
