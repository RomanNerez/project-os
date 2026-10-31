<?php

namespace App\Containers\AppSection\User\Tests\Functional\WEB;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Invitation\Tasks\CreateInvitationTask;
use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\User\Tests\Functional\WebTestCase;
use App\Containers\AppSection\User\UI\WEB\Controllers\ListUsersController;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListUsersController::class)]
final class ListUsersTest extends WebTestCase
{
    public function testGuestIsRedirected(): void
    {
        $this->get('/users')->assertRedirect();
    }

    public function testRegularUserIsForbidden(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web')
            ->get('/users')
            ->assertForbidden();
    }

    public function testUserWithViewTeamPermissionSeesUsersButCannotManageThem(): void
    {
        $this->actingAs(User::factory()->withWorkspacePermissions(Permission::TEAM_VIEW)->createOne(), 'web')
            ->get('/users')
            ->assertOk()
            ->assertInertia(static fn (AssertableInertia $page) => $page
                ->component('users')
                ->where('roles', null)
                ->where('auth.user.permissions.manage_users', true)
                ->where('auth.user.permissions.create_users', false)
                ->where('auth.user.permissions.manage_roles', false));
    }

    public function testUserWithOtherPermissionsIsForbidden(): void
    {
        $this->actingAs(User::factory()->withWorkspacePermissions(Permission::PROJECTS_CREATE)->createOne(), 'web')
            ->get('/users')
            ->assertForbidden();
    }

    public function testAdminSeesUsersWithRolesSortedByName(): void
    {
        // The seeded "Super Admin" sorts after both users below.
        $admin = User::factory()->superAdmin()->createOne(['first_name' => 'Anna', 'last_name' => 'Admin']);
        User::factory()->createOne(['first_name' => 'Bohdan', 'last_name' => 'User']);

        $this->actingAs($admin, 'web')
            ->get('/users')
            ->assertOk()
            ->assertInertia(static fn (AssertableInertia $page) => $page
                ->component('users')
                ->has('users.data', User::count())
                ->where('users.meta.pagination.total', User::count())
                ->where('users.data.0.name', 'Anna Admin')
                ->where('users.data.0.roles.data.0.name', 'admin')
                ->where('users.data.1.name', 'Bohdan User')
                ->has('users.data.1.roles.data', 0)
                ->has('roles.data', 0)
                ->where('auth.user.permissions.manage_users', true));
    }

    public function testAdminSeesOnlyPendingInvitations(): void
    {
        $admin = User::factory()->superAdmin()->createOne(['first_name' => 'Anna', 'last_name' => 'Admin']);
        $createInvitation = app(CreateInvitationTask::class);

        $createInvitation->run('expired@example.com', $admin->id)[0]->update(['expires_at' => now()->subMinute()]);
        $createInvitation->run('accepted@example.com', $admin->id)[0]->update(['accepted_at' => now()]);
        $createInvitation->run('registered@example.com', $admin->id);
        User::factory()->createOne(['email' => 'registered@example.com']);
        $createInvitation->run('pending@example.com', $admin->id);

        $this->actingAs($admin, 'web')
            ->get('/users')
            ->assertOk()
            ->assertInertia(static fn (AssertableInertia $page) => $page
                ->component('users')
                ->has('invitations.data', 1)
                ->where('invitations.data.0.email', 'pending@example.com')
                ->where('invitations.data.0.invited_by_name', 'Anna Admin'));
    }
}
