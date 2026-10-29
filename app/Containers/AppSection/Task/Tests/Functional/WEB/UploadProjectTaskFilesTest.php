<?php

namespace App\Containers\AppSection\Task\Tests\Functional\WEB;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Task\UI\WEB\Controllers\UploadProjectTaskFilesController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(UploadProjectTaskFilesController::class)]
final class UploadProjectTaskFilesTest extends WebTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');
    }

    public function testTaskCreatorUploadsWithoutProjectRole(): void
    {
        $task = $this->createTask($this->createProject(User::factory()->createOne()), creator: $this->user);

        $this->upload($task)->assertRedirect();

        self::assertCount(1, $task->getMedia());
    }

    public function testStoresFilesInProjectTaskFolder(): void
    {
        $task = $this->createTask($this->createProject($this->user));

        $this->upload($task)->assertRedirect();

        $media = $task->getFirstMedia();
        self::assertNotNull($media);
        Storage::disk('public')->assertExists(
            'projects/' . $task->project_id . '/tasks/' . $task->id . '/' . $media->id . '/notes.pdf',
        );
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
    public function testMemberWithAnyRoleUploads(ProjectRole $role): void
    {
        $project = $this->createProject(User::factory()->createOne());
        $project->members()->attach($this->user->id, ['role' => $role->value]);
        $task = $this->createTask($project);

        $this->upload($task)->assertRedirect();

        self::assertCount(1, $task->getMedia());
    }

    public function testOutsiderCannotUpload(): void
    {
        $task = $this->createTask($this->createProject(User::factory()->createOne()));

        $this->upload($task)->assertForbidden();

        self::assertCount(0, $task->getMedia());
    }

    public function testTaskFromAnotherProjectIsNotFound(): void
    {
        $task = $this->createTask($this->createProject($this->user));
        $otherProject = $this->createProject($this->user);

        $this->upload($task, $otherProject)->assertNotFound();

        self::assertCount(0, $task->getMedia());
    }

    private function upload(Task $task, ?Project $project = null): TestResponse
    {
        return $this->post(
            action(UploadProjectTaskFilesController::class, ['project' => $project->id ?? $task->project_id, 'task' => $task->id]),
            ['files' => [UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')]],
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
