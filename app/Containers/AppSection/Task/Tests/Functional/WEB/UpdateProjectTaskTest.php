<?php

namespace App\Containers\AppSection\Task\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskPriority;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Task\UI\WEB\Controllers\UpdateProjectTaskController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(UpdateProjectTaskController::class)]
final class UpdateProjectTaskTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testUpdatesOnlyTitle(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project, ['description' => 'Опис', 'status' => TaskStatus::IN_REVIEW->value]);

        $this->patchTask($project, $task, ['title' => 'Оновлена назва'])->assertRedirect();

        $task->refresh();
        self::assertSame('Оновлена назва', $task->title);
        self::assertSame('Опис', $task->description);
        self::assertSame(TaskStatus::IN_REVIEW->value, $task->status);
    }

    public function testUpdatesOnlyDescription(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['description' => '<p>Новий опис</p>'])->assertRedirect();

        $task->refresh();
        self::assertSame('<p>Новий опис</p>', $task->description);
        self::assertSame('Задача', $task->title);
    }

    public function testStoresEmptyEditorMarkupAsNoDescription(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project, ['description' => '<p>Опис</p>']);

        $this->patchTask($project, $task, ['description' => '<p></p>'])->assertRedirect();

        self::assertNull($task->refresh()->description);
    }

    public function testUpdatesOnlyStatus(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['status' => TaskStatus::DONE->value])->assertRedirect();

        $task->refresh();
        self::assertSame(TaskStatus::DONE->value, $task->status);
        self::assertSame('Задача', $task->title);
    }

    public function testUpdatesOnlyPriority(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project, ['estimate' => 90]);

        $this->patchTask($project, $task, ['priority' => TaskPriority::URGENT->value])->assertRedirect();

        $task->refresh();
        self::assertSame(TaskPriority::URGENT->value, $task->priority);
        self::assertSame(90, $task->estimate);
    }

    public function testUpdatesEstimate(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['estimate' => 150])->assertRedirect();

        $task->refresh();
        self::assertSame(150, $task->estimate);
        self::assertSame(TaskPriority::MEDIUM->value, $task->priority);
    }

    public function testClearsEstimate(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project, ['estimate' => 60]);

        $this->patchTask($project, $task, ['estimate' => null])->assertRedirect();

        self::assertNull($task->refresh()->estimate);
    }

    public function testAssignsProjectMember(): void
    {
        $project = $this->createProject($this->user);
        $member = User::factory()->createOne();
        $project->members()->attach($member->id, ['role' => ProjectRole::CLIENT->value]);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['assignee_id' => $member->id])->assertRedirect();

        self::assertSame($member->id, $task->refresh()->assignee_id);
    }

    public function testAssignsProjectOwner(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['assignee_id' => $this->user->id])->assertRedirect();

        self::assertSame($this->user->id, $task->refresh()->assignee_id);
    }

    public function testUnassignsTask(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project, ['assignee_id' => $this->user->id]);

        $this->patchTask($project, $task, ['assignee_id' => null])->assertRedirect();

        self::assertNull($task->refresh()->assignee_id);
    }

    public function testRejectsAssigneeOutsideProject(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['assignee_id' => User::factory()->createOne()->id])
            ->assertSessionHasErrors('assignee_id');

        self::assertNull($task->refresh()->assignee_id);
    }

    public function testLogsChangedFieldsWithTheirAuthor(): void
    {
        $project = $this->createProject($this->user);
        $member = User::factory()->createOne();
        $project->members()->attach($member->id, ['role' => ProjectRole::CLIENT->value]);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, [
            'status' => TaskStatus::DONE->value,
            'assignee_id' => $member->id,
        ])->assertRedirect();

        $activity = $task->activitiesAsSubject()->where('event', 'updated')->sole();
        self::assertSame($this->user->id, $activity->causer_id);
        self::assertSame(
            ['status' => TaskStatus::DONE->value, 'assignee.name' => $member->name],
            $activity->attribute_changes?->get('attributes'),
        );
        self::assertSame(
            ['status' => TaskStatus::TODO->value, 'assignee.name' => null],
            $activity->attribute_changes?->get('old'),
        );
    }

    public function testDoesNotLogUnchangedTask(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['title' => 'Задача'])->assertRedirect();

        self::assertSame(0, $task->activitiesAsSubject()->where('event', 'updated')->count());
    }

    public function testIgnoresProjectId(): void
    {
        $project = $this->createProject($this->user);
        $otherProject = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['title' => 'Задача', 'project_id' => $otherProject->id])
            ->assertRedirect();

        self::assertSame($project->id, $task->refresh()->project_id);
    }

    /**
     * @return array<string, array{ProjectRole, bool}>
     */
    public static function memberRoles(): array
    {
        return [
            'admin' => [ProjectRole::ADMIN, true],
            'executor' => [ProjectRole::EXECUTOR, true],
            'client' => [ProjectRole::CLIENT, false],
        ];
    }

    #[DataProvider('memberRoles')]
    public function testMemberRoleDecidesAccess(ProjectRole $role, bool $allowed): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => $role->value]);
        $task = $this->createTask($project);

        $response = $this->patchTask($project, $task, ['status' => TaskStatus::DONE->value]);

        $allowed ? $response->assertRedirect() : $response->assertForbidden();
        self::assertSame(
            $allowed ? TaskStatus::DONE->value : TaskStatus::TODO->value,
            $task->refresh()->status,
        );
    }

    public function testOutsiderCannotUpdateTask(): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['title' => 'Чужа правка'])->assertForbidden();

        self::assertSame('Задача', $task->refresh()->title);
    }

    public function testReturnsNotFoundForTaskOfAnotherProject(): void
    {
        $project = $this->createProject($this->user);
        $foreignTask = $this->createTask($this->createProject(User::factory()->createOne()));

        $this->patchTask($project, $foreignTask, ['title' => 'Правка'])->assertNotFound();

        self::assertSame('Задача', $foreignTask->refresh()->title);
    }

    public function testRejectsEmptyTitle(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['title' => ''])->assertSessionHasErrors('title');
    }

    public function testRejectsUnknownStatus(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['status' => 'archived'])->assertSessionHasErrors('status');
    }

    public function testRejectsUnknownPriority(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->patchTask($project, $task, ['priority' => 'critical'])->assertSessionHasErrors('priority');
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function invalidEstimates(): array
    {
        return [
            'negative' => [-5],
            'zero' => [0],
            'fractional' => [1.5],
            'not a number' => ['abc'],
            'too large' => [60001],
        ];
    }

    #[DataProvider('invalidEstimates')]
    public function testRejectsInvalidEstimate(mixed $estimate): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project, ['estimate' => 60]);

        $this->patchTask($project, $task, ['estimate' => $estimate])->assertSessionHasErrors('estimate');

        self::assertSame(60, $task->refresh()->estimate);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function patchTask(Project $project, Task $task, array $data): TestResponse
    {
        return $this->patch(
            action(UpdateProjectTaskController::class, ['project' => $project->id, 'task' => $task->id]),
            $data,
        );
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

    /**
     * @param array<string, mixed> $attributes
     */
    private function createTask(Project $project, array $attributes = []): Task
    {
        return Task::create([
            'user_id' => $project->user_id,
            'project_id' => $project->id,
            'title' => 'Задача',
            'status' => TaskStatus::TODO->value,
            ...$attributes,
        ]);
    }
}
