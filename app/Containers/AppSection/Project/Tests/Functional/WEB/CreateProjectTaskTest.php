<?php

namespace App\Containers\AppSection\Project\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Project\UI\WEB\Controllers\CreateProjectTaskController;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateProjectTaskController::class)]
final class CreateProjectTaskTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testOwnerCreatesTaskWithDefaultStatus(): void
    {
        $project = $this->createProject($this->user);

        $this->post(action(CreateProjectTaskController::class, $project), ['title' => 'Задача проєкту'])
            ->assertRedirect();

        $task = Task::query()->where('title', 'Задача проєкту')->firstOrFail();
        self::assertSame($project->id, $task->project_id);
        self::assertSame($this->user->id, $task->user_id);
        self::assertSame(TaskStatus::TODO->value, $task->status);
    }

    public function testOwnerCreatesTaskInProjectColumn(): void
    {
        $project = $this->createProject($this->user);

        $this->post(action(CreateProjectTaskController::class, $project), [
            'title' => 'Задача з канбану',
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

        $this->post(action(CreateProjectTaskController::class, $project), [
            'title' => 'Задача учасника',
        ])->assertRedirect();

        self::assertTrue(Task::query()->where('project_id', $project->id)->exists());
    }

    public function testViewerCannotCreateTaskInProject(): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => ProjectRole::VIEWER->value]);

        $this->post(action(CreateProjectTaskController::class, $project), [
            'title' => 'Задача спостерігача',
        ])->assertForbidden();

        self::assertFalse(Task::query()->where('project_id', $project->id)->exists());
    }

    public function testOutsiderCannotCreateTaskInForeignProject(): void
    {
        $project = $this->createProject(User::factory()->createOne());

        $this->post(action(CreateProjectTaskController::class, $project), [
            'title' => 'Чужа задача',
        ])->assertForbidden();

        self::assertFalse(Task::query()->where('project_id', $project->id)->exists());
    }

    public function testReturnsNotFoundForUnknownProject(): void
    {
        $this->post(action(CreateProjectTaskController::class, ['project' => 999999]), [
            'title' => 'Задача',
        ])->assertNotFound();
    }

    public function testRequiresTitle(): void
    {
        $project = $this->createProject($this->user);

        $this->post(action(CreateProjectTaskController::class, $project), [])
            ->assertSessionHasErrors('title');
    }

    public function testRejectsUnknownStatus(): void
    {
        $project = $this->createProject($this->user);

        $this->post(action(CreateProjectTaskController::class, $project), [
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
