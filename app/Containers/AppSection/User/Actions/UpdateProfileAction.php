<?php

namespace App\Containers\AppSection\User\Actions;

use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\User\Tasks\UpdateUserTask;
use App\Containers\AppSection\User\UI\WEB\Requests\UpdateProfileRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

final class UpdateProfileAction extends ParentAction
{
    public function __construct(
        private readonly UpdateUserTask $updateUserTask,
    ) {
    }

    /**
     * A new avatar replaces the old one (single-file collection); remove_avatar only applies without an upload.
     */
    public function run(UpdateProfileRequest $request, int $userId): User
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data, $userId): User {
            $user = $this->updateUserTask->run($userId, [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
            ]);

            if ($request->hasFile('avatar')) {
                $user->addMediaFromRequest('avatar')->toMediaCollection(User::AVATAR_COLLECTION);
            } elseif ($request->boolean('remove_avatar')) {
                $user->clearMediaCollection(User::AVATAR_COLLECTION);
            }

            return $user;
        });
    }
}
