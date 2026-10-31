<?php

namespace App\Containers\AppSection\Project\Tests\Functional\WEB;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Project\UI\WEB\Controllers\CreateProjectController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateProjectController::class)]
final class CreateProjectTest extends WebTestCase
{
    public function testUserWithoutPermissionIsForbidden(): void
    {
        $this->actingAs(User::factory()->createOne(), 'web');

        $this->store()->assertForbidden();

        self::assertSame(0, Project::count());
    }

    public function testUserWithCreateProjectsPermissionCreatesProject(): void
    {
        $user = User::factory()->withWorkspacePermissions(Permission::PROJECTS_CREATE)->createOne();
        $this->actingAs($user, 'web');

        $this->store()->assertRedirect()->assertSessionHasNoErrors();

        self::assertTrue(Project::where('title', 'Новий сайт')->sole()->isOwnedBy($user));
    }

    public function testUserWithOnlyViewTeamPermissionIsForbidden(): void
    {
        $this->actingAs(User::factory()->withWorkspacePermissions(Permission::TEAM_VIEW)->createOne(), 'web');

        $this->store()->assertForbidden();
    }

    public function testAdminCreatesProject(): void
    {
        $this->actingAs(User::factory()->superAdmin()->createOne(), 'web');

        $this->store()->assertRedirect()->assertSessionHasNoErrors();

        self::assertSame(1, Project::count());
    }

    private function store(): TestResponse
    {
        return $this->post('/projects', [
            'title' => 'Новий сайт',
            'description' => 'Опис',
            'status' => ProjectStatus::cases()[0]->value,
            'budget' => 1000,
            'active_until' => now()->addMonth()->toDateString(),
        ]);
    }
}
