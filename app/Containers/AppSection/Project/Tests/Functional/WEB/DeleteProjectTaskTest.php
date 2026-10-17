<?php

namespace App\Containers\AppSection\Project\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Project\UI\WEB\Controllers\DeleteProjectTaskController;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(DeleteProjectTaskController::class)]
final class DeleteProjectTaskTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testOwnerDeletesTask(): void
    {
        $project = $this->createProject($this->user);
        $task = $this->createTask($project);

        $this->deleteTask($project, $task)->assertRedirect();

        $this->assertModelMissing($task);
    }

    /**
     * @return array<string, array{ProjectRole, bool}>
     */
    public static function memberRoles(): array
    {
        return [
            'admin' => [ProjectRole::ADMIN, true],
            'member' => [ProjectRole::MEMBER, false],
            'viewer' => [ProjectRole::VIEWER, false],
        ];
    }

    #[DataProvider('memberRoles')]
    public function testMemberRoleDecidesAccess(ProjectRole $role, bool $allowed): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => $role->value]);
        $task = $this->createTask($project);

        $response = $this->deleteTask($project, $task);

        if ($allowed) {
            $response->assertRedirect();
            $this->assertModelMissing($task);
        } else {
            $response->assertForbidden();
            $this->assertModelExists($task);
        }
    }

    public function testOutsiderCannotDeleteTask(): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $task = $this->createTask($project);

        $this->deleteTask($project, $task)->assertForbidden();

        $this->assertModelExists($task);
    }

    public function testReturnsNotFoundForTaskOfAnotherProject(): void
    {
        $project = $this->createProject($this->user);
        $foreignTask = $this->createTask($this->createProject(User::factory()->createOne()));

        $this->deleteTask($project, $foreignTask)->assertNotFound();

        $this->assertModelExists($foreignTask);
    }

    private function deleteTask(Project $project, Task $task): TestResponse
    {
        return $this->delete(
            action(DeleteProjectTaskController::class, ['project' => $project->id, 'task' => $task->id]),
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

    private function createTask(Project $project): Task
    {
        return Task::create([
            'user_id' => $project->user_id,
            'project_id' => $project->id,
            'title' => 'Задача',
            'status' => TaskStatus::TODO->value,
        ]);
    }
}
