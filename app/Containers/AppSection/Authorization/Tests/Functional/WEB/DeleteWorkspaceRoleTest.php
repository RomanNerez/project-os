<?php

namespace App\Containers\AppSection\Authorization\Tests\Functional\WEB;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Enums\Role as RoleEnum;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\Authorization\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Authorization\UI\WEB\Controllers\DeleteWorkspaceRoleController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DeleteWorkspaceRoleController::class)]
final class DeleteWorkspaceRoleTest extends WebTestCase
{
    public function testAdminDeletesRoleAndUsersLoseItsPermissions(): void
    {
        $member = User::factory()->withWorkspacePermissions(Permission::PROJECTS_CREATE)->createOne();
        $role = $member->roles()->sole();
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->delete("/settings/roles/{$role->getHashedKey()}")->assertRedirect();

        self::assertNull(Role::find($role->id));
        self::assertFalse($member->fresh()->checkPermissionTo(Permission::PROJECTS_CREATE->value, Permission::GUARD));
    }

    public function testSystemAdminRoleCannotBeDeleted(): void
    {
        $admin = Role::findByName(RoleEnum::SUPER_ADMIN->value, Permission::GUARD);
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->delete("/settings/roles/{$admin->getHashedKey()}")->assertForbidden();

        self::assertNotNull(Role::find($admin->id));
    }

    public function testRegularUserIsForbidden(): void
    {
        $role = User::factory()->withWorkspacePermissions()->createOne()->roles()->sole();
        $this->actingAs(User::factory()->createOne(), 'web');

        $this->delete("/settings/roles/{$role->getHashedKey()}")->assertForbidden();
    }
}
