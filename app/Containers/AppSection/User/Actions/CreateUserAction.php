<?php

namespace App\Containers\AppSection\User\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\User\Tasks\CreateUserTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

final class CreateUserAction extends ParentAction
{
    public function __construct(
        private readonly CreateUserTask $createUserTask,
    ) {
    }

    /**
     * Users created by an admin skip email verification.
     */
    public function run(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = $this->createUserTask->run($data);
            $user->markEmailAsVerified();

            return $user;
        });
    }
}
