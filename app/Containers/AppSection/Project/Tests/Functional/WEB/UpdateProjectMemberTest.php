<?php

namespace App\Containers\AppSection\Project\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Project\UI\WEB\Controllers\UpdateProjectMemberController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(UpdateProjectMemberController::class)]
final class UpdateProjectMemberTest extends WebTestCase
{
    private User $user;
    private User $owner;
    private User $member;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->owner = User::factory()->createOne();
        $this->member = User::factory()->createOne();
        $this->actingAs($this->user, 'web');

        $this->project = Project::create([
            'user_id' => $this->owner->id,
            'title' => 'Проєкт',
            'description' => 'Тестовий проєкт',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'budget' => 1000,
        ]);
        $this->project->members()->attach($this->member->id, ['role' => ProjectRole::EXECUTOR->value]);
    }

    public function testOwnerUpdatesRoleAndHourlyRate(): void
    {
        $this->actingAs($this->owner, 'web');

        $this->updateMember($this->member, ['role' => ProjectRole::ADMIN->value, 'hourly_rate' => 50])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('project_user', [
            'project_id' => $this->project->id,
            'user_id' => $this->member->id,
            'role' => ProjectRole::ADMIN->value,
            'hourly_rate' => 50,
        ]);
    }

    public function testAdminCanClearHourlyRate(): void
    {
        $this->project->members()->attach($this->user->id, ['role' => ProjectRole::ADMIN->value]);
        $this->project->members()->updateExistingPivot($this->member->id, ['hourly_rate' => 20]);

        $this->updateMember($this->member, ['role' => ProjectRole::EXECUTOR->value, 'hourly_rate' => null])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('project_user', [
            'user_id' => $this->member->id,
            'hourly_rate' => null,
        ]);
    }

    public function testRejectsNegativeHourlyRate(): void
    {
        $this->actingAs($this->owner, 'web');

        $this->updateMember($this->member, ['role' => ProjectRole::EXECUTOR->value, 'hourly_rate' => -5])
            ->assertSessionHasErrors('hourly_rate');
    }

    /**
     * @return array<string, array{ProjectRole}>
     */
    public static function nonManagingRoles(): array
    {
        return [
            'executor' => [ProjectRole::EXECUTOR],
            'client' => [ProjectRole::CLIENT],
        ];
    }

    #[DataProvider('nonManagingRoles')]
    public function testNonManagingMemberCannotUpdate(ProjectRole $role): void
    {
        $this->project->members()->attach($this->user->id, ['role' => $role->value]);

        $this->updateMember($this->member, ['role' => ProjectRole::EXECUTOR->value, 'hourly_rate' => 100])
            ->assertForbidden();
    }

    public function testCannotUpdateProjectOwner(): void
    {
        $this->project->members()->attach($this->user->id, ['role' => ProjectRole::ADMIN->value]);

        $this->updateMember($this->owner, ['role' => ProjectRole::CLIENT->value])
            ->assertForbidden();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function updateMember(User $member, array $data): TestResponse
    {
        return $this->patch(
            action(UpdateProjectMemberController::class, ['project' => $this->project->id, 'member' => $member->id]),
            $data,
        );
    }
}
