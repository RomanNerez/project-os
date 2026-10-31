<?php

namespace App\Containers\AppSection\Authorization\Tasks;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Str;

final class SaveWorkspaceRoleTask extends ParentTask
{
    public function __construct(
        private readonly CreateRoleTask $createRoleTask,
    ) {
    }

    /**
     * Creates the role when none is given, otherwise updates it, then syncs its permissions.
     *
     * @param array{display_name: string, description?: string|null, permissions?: list<string>} $data
     */
    public function run(array $data, Role|null $role = null): Role
    {
        if (null === $role) {
            $role = $this->createRoleTask->run(
                $this->generateName($data['display_name']),
                $data['description'] ?? null,
                $data['display_name'],
                Permission::GUARD,
            );
        } else {
            $role->update([
                'display_name' => $data['display_name'],
                'description' => $data['description'] ?? null,
            ]);
        }

        return $role->syncPermissions($data['permissions'] ?? []);
    }

    private function generateName(string $displayName): string
    {
        $slug = Str::slug($displayName);

        return ('' === $slug ? 'role' : $slug) . '-' . Str::lower(Str::random(6));
    }
}
