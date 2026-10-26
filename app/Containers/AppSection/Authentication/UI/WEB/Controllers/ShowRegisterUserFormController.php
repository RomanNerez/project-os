<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Controllers;

use App\Containers\AppSection\Invitation\Tasks\FindUsableInvitationByTokenTask;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ShowRegisterUserFormController extends WebController
{
    public function __construct(
        private readonly FindUsableInvitationByTokenTask $findInvitationTask,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $token = $request->string('token')->toString();
        $invitation = $this->findInvitationTask->run($token);

        return Inertia::render('register', [
            'invitation' => null !== $invitation ? ['token' => $token, 'email' => $invitation->email] : null,
        ]);
    }
}
