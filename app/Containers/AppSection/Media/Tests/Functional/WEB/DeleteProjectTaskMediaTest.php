<?php

namespace App\Containers\AppSection\Media\Tests\Functional\WEB;

use App\Containers\AppSection\Media\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Media\UI\WEB\Controllers\DeleteProjectTaskMediaController;
use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[CoversClass(DeleteProjectTaskMediaController::class)]
final class DeleteProjectTaskMediaTest extends WebTestCase
{
    private User $user;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->createOne();
        $this->actingAs($this->user, 'web');

        $this->project = Project::create([
            'user_id' => User::factory()->createOne()->id,
            'title' => 'Проєкт',
            'description' => 'Тестовий проєкт',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'budget' => 1000,
        ]);
    }

    public function testTaskCreatorDeletesFileWithoutProjectRole(): void
    {
        $media = $this->createMedia(creator: $this->user);

        $this->deleteMedia($media)->assertRedirect();

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
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
        $this->project->members()->attach($this->user->id, ['role' => $role->value]);
        $media = $this->createMedia();

        $response = $this->deleteMedia($media);

        if ($allowed) {
            $response->assertRedirect();
            $this->assertDatabaseMissing('media', ['id' => $media->id]);
        } else {
            $response->assertForbidden();
            $this->assertDatabaseHas('media', ['id' => $media->id]);
        }
    }

    public function testOutsiderCannotDeleteFile(): void
    {
        $media = $this->createMedia();

        $this->deleteMedia($media)->assertForbidden();

        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    public function testFileFromAnotherTaskIsNotFound(): void
    {
        $media = $this->createMedia(creator: $this->user);
        $otherTask = $this->createMedia(creator: $this->user)->model;
        self::assertInstanceOf(Task::class, $otherTask);

        $this->deleteMedia($media, $otherTask)->assertNotFound();

        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    private function deleteMedia(Media $media, ?Task $task = null): TestResponse
    {
        return $this->delete(action(DeleteProjectTaskMediaController::class, [
            'project' => $this->project->id,
            'task' => $task->id ?? $media->model_id,
            'media' => $media->id,
        ]));
    }

    private function createMedia(?User $creator = null): Media
    {
        $task = Task::create([
            'user_id' => ($creator ?? $this->project->user)->id,
            'project_id' => $this->project->id,
            'title' => 'Задача',
            'status' => TaskStatus::TODO->value,
        ]);

        return $task
            ->addMedia(UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'))
            ->toMediaCollection();
    }
}
