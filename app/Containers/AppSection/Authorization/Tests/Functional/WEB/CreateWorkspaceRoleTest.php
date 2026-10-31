<?php

namespace App\Containers\AppSection\Authorization\Tests\Functional\WEB;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Models\Role;
use App\Containers\AppSection\Authorization\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Authorization\UI\WEB\Controllers\CreateWorkspaceRoleController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateWorkspaceRoleController::class)]
final class CreateWorkspaceRoleTest extends WebTestCase
{
    public function testAdminCreatesWebRoleWithPermissions(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->store([])->assertRedirect()->assertSessionHasNoErrors();

        $role = Role::where('display_name', 'Менеджер')->sole();

        self::assertSame(Permission::GUARD, $role->guard_name);
        self::assertSame('Керує проєктами', $role->description);
        self::assertTrue($role->hasPermissionTo(Permission::PROJECTS_CREATE->value, Permission::GUARD));
        self::assertFalse($role->hasPermissionTo(Permission::TEAM_VIEW->value, Permission::GUARD));
    }

    public function testRejectsDuplicateNameAndUnknownPermission(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');
        $this->store([]);

        $this->store(['permissions' => ['delete-everything']])
            ->assertSessionHasErrors(['display_name', 'permissions.0']);

        self::assertSame(1, Role::where('display_name', 'Менеджер')->count());
    }

    public function testRegularUserIsForbidden(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        $this->store([])->assertForbidden();

        self::assertSame(0, Role::where('display_name', 'Менеджер')->count());
    }

    private function store(array $overrides): TestResponse
    {
        return $this->post('/settings/roles', [
            'display_name' => 'Менеджер',
            'description' => 'Керує проєктами',
            'permissions' => [Permission::PROJECTS_CREATE->value],
            ...$overrides,
        ]);
    }
}
