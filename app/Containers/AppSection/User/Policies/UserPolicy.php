<?php

namespace App\Containers\AppSection\User\Policies;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\User\Data\Repositories\UserRepository;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Policies\Policy as ParentPolicy;

final class UserPolicy extends ParentPolicy
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function before(User $user): bool|null
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function delete(): bool
    {
        return false;
    }

    public function show(User $user, int $userId): bool
    {
        $entity = $this->userRepository->findById($userId);

        return $user->is($entity);
    }

    public function index(User $user): bool
    {
        return $user->checkPermissionTo(Permission::TEAM_VIEW->value, Permission::GUARD);
    }

    public function create(): bool
    {
        return false;
    }

    public function update(User $user, int $userId): bool
    {
        $entity = $this->userRepository->findById($userId);

        return $user->is($entity);
    }
}
