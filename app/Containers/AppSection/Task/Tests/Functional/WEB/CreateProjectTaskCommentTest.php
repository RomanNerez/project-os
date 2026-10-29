<?php

namespace App\Containers\AppSection\Task\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Task\UI\WEB\Controllers\CreateProjectTaskCommentController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(CreateProjectTaskCommentController::class)]
final class CreateProjectTaskCommentTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testProjectOwnerComments(): void
    {
        $task = $this->createTask($this->createProject($this->user));

        $this->comment($task)->assertRedirect();

        $this->assertCommented($task);
    }

    public function testTaskCreatorCommentsWithoutProjectRole(): void
    {
        $task = $this->createTask($this->createProject(User::factory()->createOne()), creator: $this->user);

        $this->comment($task)->assertRedirect();

        $this->assertCommented($task);
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
    public function testMemberWithAnyRoleComments(ProjectRole $role): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => $role->value]);
        $task = $this->createTask($project);

        $this->comment($task)->assertRedirect();

        $this->assertCommented($task);
    }

    public function testOutsiderCannotComment(): void
    {
        $task = $this->createTask($this->createProject(User::factory()->createOne()));

        $this->comment($task)->assertForbidden();

        $this->assertDatabaseMissing('comments', ['model_id' => $task->id]);
    }

    public function testTaskFromAnotherProjectIsNotFound(): void
    {
        $task = $this->createTask($this->createProject($this->user));
        $otherProject = $this->createProject($this->user);

        $this->comment($task, $otherProject)->assertNotFound();

        $this->assertDatabaseMissing('comments', ['model_id' => $task->id]);
    }

    private function comment(Task $task, ?Project $project = null): TestResponse
    {
        return $this->post(
            action(CreateProjectTaskCommentController::class, ['project' => $project->id ?? $task->project_id, 'task' => $task->id]),
            ['body' => 'Коментар'],
        );
    }

    private function assertCommented(Task $task): void
    {
        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'model_type' => Task::class,
            'model_id' => $task->id,
            'body' => 'Коментар',
        ]);
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

    private function createTask(Project $project, ?User $creator = null): Task
    {
        return Task::create([
            'user_id' => ($creator ?? $project->user)->id,
            'project_id' => $project->id,
            'title' => 'Задача',
            'status' => TaskStatus::TODO->value,
        ]);
    }
}
