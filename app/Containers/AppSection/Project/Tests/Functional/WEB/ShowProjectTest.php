<?php

namespace App\Containers\AppSection\Project\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Project\UI\WEB\Controllers\ShowProjectController;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
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
            'executor' => [ProjectRole::EXECUTOR],
            'client' => [ProjectRole::CLIENT],
        ];
    }

    #[DataProvider('memberRoles')]
    public function testMemberWithAnyRoleSeesProject(ProjectRole $role): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => $role->value]);

        $this->showProject($project->id)->assertOk();
    }

    public function testTasksIncludeFinishedTimeEntries(): void
    {
        $project = $this->createProject($this->user);
        $task = Task::create(['user_id' => $this->user->id, 'project_id' => $project->id, 'title' => 'Задача']);
        foreach ([['2026-10-28 10:00:00', '2026-10-28 10:45:00'], ['2026-10-28 11:00:00', null]] as [$startedAt, $stoppedAt]) {
            TimeEntry::create([
                'user_id' => $this->user->id,
                'project_id' => $project->id,
                'task_id' => $task->id,
                'description' => $task->title,
                'started_at' => $startedAt,
                'stopped_at' => $stoppedAt,
            ]);
        }

        $this->showProject($project->id)
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->has('tasks.data.0.time_entries.data', 1)
                    ->where('tasks.data.0.time_entries.data.0.duration', 2700)
                    ->where('tasks.data.0.time_entries.data.0.user.data.name', $this->user->name),
            );
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
