<?php

namespace App\Containers\AppSection\Authorization\Tests\Functional\WEB;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Authorization\Tasks\SaveWorkspaceRoleTask;
use App\Containers\AppSection\Authorization\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Authorization\UI\WEB\Controllers\ListWorkspaceRolesController;
use App\Containers\AppSection\User\Models\User;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListWorkspaceRolesController::class)]
final class ListWorkspaceRolesTest extends WebTestCase
{
    public function testGuestIsRedirected(): void
    {
        $this->get('/settings/roles')->assertRedirect();
    }

    public function testSettingsRedirectsToRolesTab(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web')
            ->get('/settings')
            ->assertRedirect('/settings/roles');
    }

    public function testRegularUserIsForbidden(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web')
            ->get('/settings/roles')
            ->assertForbidden();
    }

    public function testUserWithWorkspacePermissionsIsForbidden(): void
    {
        $user = User::factory()->withWorkspacePermissions(...Permission::cases())->createOne();

        $this->actingAs($user, 'web')
            ->get('/settings/roles')
            ->assertForbidden();
    }

    public function testAdminOpensPermissionsTab(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web')
            ->get('/settings/permissions')
            ->assertOk()
            ->assertInertia(static fn (AssertableInertia $page) => $page
                ->component('settings')
                ->where('tab', 'permissions')
                ->has('roles.data')
                ->has('permissions', count(Permission::cases())));
    }

    public function testRegularUserCannotOpenPermissionsTab(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web')
            ->get('/settings/permissions')
            ->assertForbidden();
    }

    public function testAdminSeesWorkspaceRolesWithoutSystemRole(): void
    {
        app(SaveWorkspaceRoleTask::class)->run([
            'display_name' => 'Менеджер',
            'permissions' => [Permission::PROJECTS_CREATE->value],
        ]);

        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web')
            ->get('/settings/roles')
            ->assertOk()
            ->assertInertia(static fn (AssertableInertia $page) => $page
                ->component('settings')
                ->where('tab', 'roles')
                ->has('roles.data', 1)
                ->where('roles.data.0.display_name', 'Менеджер')
                ->where('roles.data.0.permissions', [Permission::PROJECTS_CREATE->value])
                ->where('roles.data.0.users_count', 0)
                ->has('permissions', count(Permission::cases()))
                ->where('auth.user.permissions.manage_roles', true));
    }
}
