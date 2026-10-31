<?php

namespace App\Containers\AppSection\Authorization\Tests\Functional\WEB;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Enums\Role as RoleEnum;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\Authorization\Tasks\SaveWorkspaceRoleTask;
use App\Containers\AppSection\Authorization\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Authorization\UI\WEB\Controllers\UpdateUserWorkspaceRoleController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateUserWorkspaceRoleController::class)]
final class UpdateUserWorkspaceRoleTest extends WebTestCase
{
    public function testAdminReplacesUsersWorkspaceRole(): void
    {
        $user = User::factory()->withWorkspacePermissions(Permission::TEAM_VIEW)->createOne();
        $role = $this->createRole('Менеджер');
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->put("/users/{$user->id}/role", ['role_id' => $role->getHashedKey()])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        self::assertSame([$role->id], $user->fresh()->roles->pluck('id')->all());
    }

    public function testAdminClearsUsersWorkspaceRole(): void
    {
        $user = User::factory()->withWorkspacePermissions(Permission::TEAM_VIEW)->createOne();
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->put("/users/{$user->id}/role", ['role_id' => null])->assertSessionHasNoErrors();

        self::assertCount(0, $user->fresh()->roles);
    }

    public function testKeepsSuperAdminRole(): void
    {
        $user = User::factory()->superAdmin()->createOne();
        $role = $this->createRole('Менеджер');
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->put("/users/{$user->id}/role", ['role_id' => $role->getHashedKey()])->assertSessionHasNoErrors();

        self::assertTrue($user->fresh()->isSuperAdmin());
    }

    public function testCannotAssignSystemAdminRole(): void
    {
        $user = User::factory()->createOne();
        $admin = Role::findByName(RoleEnum::SUPER_ADMIN->value, Permission::GUARD);
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->put("/users/{$user->id}/role", ['role_id' => $admin->getHashedKey()])
            ->assertSessionHasErrors('role_id');

        self::assertCount(0, $user->fresh()->roles);
    }

    public function testRegularUserIsForbidden(): void
    {
        $user = User::factory()->createOne();
        $role = $this->createRole('Менеджер');
        $this->actingAs(User::factory()->withWorkspacePermissions(...Permission::cases())->createOne(), 'web');

        $this->put("/users/{$user->id}/role", ['role_id' => $role->getHashedKey()])->assertForbidden();
    }

    private function createRole(string $displayName): Role
    {
        return app(SaveWorkspaceRoleTask::class)->run(['display_name' => $displayName, 'permissions' => []]);
    }
}
