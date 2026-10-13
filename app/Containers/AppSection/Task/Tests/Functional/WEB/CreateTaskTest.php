<?php

namespace App\Containers\AppSection\Task\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Task\UI\WEB\Controllers\CreateTaskController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateTaskController::class)]
final class CreateTaskTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testCreatesTaskWithoutProjectWithDefaultStatus(): void
    {
        $this->post(action(CreateTaskController::class), ['title' => 'Особиста задача'])
            ->assertRedirect();

        $task = Task::query()->where('title', 'Особиста задача')->firstOrFail();
        self::assertNull($task->project_id);
        self::assertSame($this->user->id, $task->user_id);
        self::assertSame(TaskStatus::TODO->value, $task->status);
    }

    public function testOwnerCreatesTaskInProjectColumn(): void
    {
        $project = $this->createProject($this->user);

        $this->post(action(CreateTaskController::class), [
            'title' => 'Задача з канбану',
            'project_id' => $project->id,
            'status' => TaskStatus::IN_REVIEW->value,
        ])->assertRedirect();

        $task = Task::query()->where('title', 'Задача з канбану')->firstOrFail();
        self::assertSame($project->id, $task->project_id);
        self::assertSame(TaskStatus::IN_REVIEW->value, $task->status);
    }

    public function testMemberCreatesTaskInProject(): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => ProjectRole::MEMBER->value]);

        $this->post(action(CreateTaskController::class), [
            'title' => 'Задача учасника',
            'project_id' => $project->id,
        ])->assertRedirect();

        self::assertTrue(Task::query()->where('project_id', $project->id)->exists());
    }

    public function testOutsiderCannotCreateTaskInForeignProject(): void
    {
        $project = $this->createProject(User::factory()->createOne());

        $this->post(action(CreateTaskController::class), [
            'title' => 'Чужа задача',
            'project_id' => $project->id,
        ])->assertForbidden();

        self::assertFalse(Task::query()->where('project_id', $project->id)->exists());
    }

    public function testRejectsUnknownStatus(): void
    {
        $this->post(action(CreateTaskController::class), [
            'title' => 'Задача',
            'status' => 'archived',
        ])->assertSessionHasErrors('status');
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
