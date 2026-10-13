<?php

namespace App\Containers\AppSection\Task\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Task\UI\WEB\Controllers\UpdateTaskStatusController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateTaskStatusController::class)]
final class UpdateTaskStatusTest extends WebTestCase
{
    private User $user;
    private Project $project;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');

        $owner = User::factory()->createOne();
        $this->project = Project::create([
            'user_id' => $owner->id,
            'title' => 'Проєкт',
            'description' => 'Тестовий проєкт',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'budget' => 1000,
        ]);
        $this->task = Task::create([
            'user_id' => $owner->id,
            'project_id' => $this->project->id,
            'title' => 'Задача',
            'status' => TaskStatus::TODO->value,
        ]);
    }

    public function testMemberMovesTaskToAnotherColumn(): void
    {
        $this->project->members()->attach($this->user->id, ['role' => ProjectRole::MEMBER->value]);

        $this->patch(action(UpdateTaskStatusController::class, ['id' => $this->task->id]), [
            'status' => TaskStatus::DONE->value,
        ])->assertRedirect();

        self::assertSame(TaskStatus::DONE->value, $this->task->refresh()->status);
    }

    public function testViewerCannotChangeStatus(): void
    {
        $this->project->members()->attach($this->user->id, ['role' => ProjectRole::VIEWER->value]);

        $this->patch(action(UpdateTaskStatusController::class, ['id' => $this->task->id]), [
            'status' => TaskStatus::DONE->value,
        ])->assertForbidden();

        self::assertSame(TaskStatus::TODO->value, $this->task->refresh()->status);
    }

    public function testOutsiderCannotChangeStatus(): void
    {
        $this->patch(action(UpdateTaskStatusController::class, ['id' => $this->task->id]), [
            'status' => TaskStatus::DONE->value,
        ])->assertForbidden();
    }
}
