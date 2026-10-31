<?php

namespace App\Containers\AppSection\Authorization\Tests\Functional\WEB;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Enums\Role as RoleEnum;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\Authorization\Tasks\SaveWorkspaceRoleTask;
use App\Containers\AppSection\Authorization\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Authorization\UI\WEB\Controllers\UpdateWorkspaceRoleController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateWorkspaceRoleController::class)]
final class UpdateWorkspaceRoleTest extends WebTestCase
{
    public function testAdminRenamesRoleAndSyncsPermissions(): void
    {
        $role = $this->createRole('Менеджер', [Permission::PROJECTS_CREATE]);
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->put("/settings/roles/{$role->getHashedKey()}", [
            'display_name' => 'Менеджер команди',
            'description' => null,
            'permissions' => [Permission::TEAM_VIEW->value],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $role->refresh();
        self::assertSame('Менеджер команди', $role->display_name);
        self::assertSame([Permission::TEAM_VIEW->value], $role->permissions->pluck('name')->all());
    }

    public function testKeepsOwnNameButRejectsAnotherRolesName(): void
    {
        $role = $this->createRole('Менеджер', []);
        $this->createRole('Гість', []);
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->put("/settings/roles/{$role->getHashedKey()}", ['display_name' => 'Менеджер', 'permissions' => []])
            ->assertSessionHasNoErrors();
        $this->put("/settings/roles/{$role->getHashedKey()}", ['display_name' => 'Гість', 'permissions' => []])
            ->assertSessionHasErrors('display_name');
    }

    public function testSystemAdminRoleCannotBeChanged(): void
    {
        $admin = Role::findByName(RoleEnum::SUPER_ADMIN->value, Permission::GUARD);
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->put("/settings/roles/{$admin->getHashedKey()}", ['display_name' => 'Hacked', 'permissions' => []])
            ->assertForbidden();
    }

    public function testRegularUserIsForbidden(): void
    {
        $role = $this->createRole('Менеджер', []);
        $this->actingAs(User::factory()->createOne(), 'web');

        $this->put("/settings/roles/{$role->getHashedKey()}", ['display_name' => 'Інша', 'permissions' => []])
            ->assertForbidden();
    }

    /**
     * @param list<Permission> $permissions
     */
    private function createRole(string $displayName, array $permissions): Role
    {
        return app(SaveWorkspaceRoleTask::class)->run([
            'display_name' => $displayName,
            'permissions' => array_map(static fn (Permission $permission) => $permission->value, $permissions),
        ]);
    }
}
