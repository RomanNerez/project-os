<?php

namespace App\Containers\AppSection\Task\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Task\UI\WEB\Controllers\UpdateTaskController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(UpdateTaskController::class)]
final class UpdateTaskTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testOwnerUpdatesProjectTask(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask(User::factory()->createOne(), $project);

        $this->put(action(UpdateTaskController::class, ['id' => $task->id]), [
            'title' => 'Оновлена назва',
            'project_id' => $project->id,
            'status' => TaskStatus::IN_PROGRESS->value,
        ])->assertRedirect();

        $task->refresh();
        self::assertSame('Оновлена назва', $task->title);
        self::assertSame($project->id, $task->project_id);
        self::assertSame(TaskStatus::IN_PROGRESS->value, $task->status);
    }

    /**
     * @return array<string, array{ProjectRole, bool}>
     */
    public static function memberRoles(): array
    {
        return [
            'admin' => [ProjectRole::ADMIN, true],
            'member' => [ProjectRole::MEMBER, true],
            'viewer' => [ProjectRole::VIEWER, false],
        ];
    }

    #[DataProvider('memberRoles')]
    public function testMemberRoleDecidesAccess(ProjectRole $role, bool $allowed): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => $role->value]);
        $task = $this->createTask($project->user, $project);

        $response = $this->put(action(UpdateTaskController::class, ['id' => $task->id]), [
            'title' => 'Оновлена назва',
            'project_id' => $project->id,
        ]);

        $allowed ? $response->assertRedirect() : $response->assertForbidden();
        self::assertSame($allowed ? 'Оновлена назва' : 'Задача', $task->refresh()->title);
    }

    public function testOutsiderCannotUpdateProjectTask(): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $task = $this->createTask($project->user, $project);

        $this->put(action(UpdateTaskController::class, ['id' => $task->id]), [
            'title' => 'Чужа правка',
        ])->assertForbidden();
    }

    public function testCannotMoveTaskIntoForeignProject(): void
    {
        $project = $this->createProject($this->user);
        $foreignProject = $this->createProject(User::factory()->createOne());
        $task = $this->createTask($this->user, $project);

        $this->put(action(UpdateTaskController::class, ['id' => $task->id]), [
            'title' => 'Задача',
            'project_id' => $foreignProject->id,
        ])->assertForbidden();

        self::assertSame($project->id, $task->refresh()->project_id);
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

    private function createTask(User $creator, Project $project): Task
    {
        return Task::create([
            'user_id' => $creator->id,
            'project_id' => $project->id,
            'title' => 'Задача',
            'status' => TaskStatus::TODO->value,
        ]);
    }
}
