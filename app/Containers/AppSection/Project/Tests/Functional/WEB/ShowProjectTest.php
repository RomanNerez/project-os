<?php

namespace App\Containers\AppSection\Project\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Project\UI\WEB\Controllers\ShowProjectController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(ShowProjectController::class)]
final class ShowProjectTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testOwnerSeesProject(): void
    {
        $project = $this->createProject($this->user);

        $this->showProject($project->id)->assertOk();
    }

    /**
     * @return array<string, array{ProjectRole}>
     */
    public static function memberRoles(): array
    {
        return [
            'admin' => [ProjectRole::ADMIN],
            'member' => [ProjectRole::MEMBER],
            'viewer' => [ProjectRole::VIEWER],
        ];
    }

    #[DataProvider('memberRoles')]
    public function testMemberWithAnyRoleSeesProject(ProjectRole $role): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => $role->value]);

        $this->showProject($project->id)->assertOk();
    }

    public function testOutsiderCannotSeeProject(): void
    {
        $project = $this->createProject(User::factory()->createOne());

        $this->showProject($project->id)->assertForbidden();
    }

    public function testReturnsNotFoundForMissingProject(): void
    {
        $this->showProject(999_999)->assertNotFound();
    }

    private function showProject(int $id): TestResponse
    {
        return $this->get(action(ShowProjectController::class, ['id' => $id]));
    }

    private function createProject(User $owner): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'title' => 'Проєкт',
            'description' => 'Тестовий проєкт',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'budget' => 1000,
        ]);
    }
}
