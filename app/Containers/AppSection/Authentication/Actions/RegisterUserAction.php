<?php

namespace App\Containers\AppSection\Authentication\Actions;

use App\Containers\AppSection\Authentication\UI\WEB\Requests\RegisterUserRequest;
use App\Containers\AppSection\Invitation\Tasks\AcceptInvitationTask;
use App\Containers\AppSection\Invitation\Tasks\FindUsableInvitationByTokenTask;
use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\User\Tasks\CreateUserTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RegisterUserAction extends ParentAction
{
    public function __construct(
        private readonly CreateUserTask $createUserTask,
        private readonly FindUsableInvitationByTokenTask $findInvitationTask,
        private readonly AcceptInvitationTask $acceptInvitationTask,
    ) {
    }

    /**
     * Registration is invite-only: the email comes from the invitation, and the invitation is consumed.
     */
    public function run(RegisterUserRequest $request): User
    {
        $data = $request->only(['first_name', 'last_name', 'password', 'birth', 'gender']);

        $user = DB::transaction(function () use ($request, $data): User {
            $invitation = $this->findInvitationTask->run($request->string('token')->toString(), lock: true)
                ?? throw ValidationException::withMessages(['token' => 'Запрошення недійсне або прострочене.']);

            $user = $this->createUserTask->run([...$data, 'email' => $invitation->email]);
            // The invitation link was delivered to this address, so it is already verified.
            $user->markEmailAsVerified();

            $this->acceptInvitationTask->run($invitation);

            return $user;
        });

        event(new Registered($user));

        return $user;
    }
}
