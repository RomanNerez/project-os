<?php

namespace App\Containers\AppSection\Comment\Tests\Functional\WEB;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Comment\Tests\Functional\WebTestCase;
use App\Containers\AppSection\Comment\UI\WEB\Controllers\DeleteProjectTaskCommentController;
use App\Containers\AppSection\Comment\UI\WEB\Controllers\UpdateProjectTaskCommentController;
use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(UpdateProjectTaskCommentController::class)]
#[CoversClass(DeleteProjectTaskCommentController::class)]
final class CommentAccessTest extends WebTestCase
{
    private User $user;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function testAuthorUpdatesOwnComment(): void
    {
        $comment = $this->createComment($this->user);

        $this->updateComment($comment)->assertRedirect();

        self::assertSame('Змінено', $comment->refresh()->body);
    }

    public function testProjectAdminCannotUpdateForeignComment(): void
    {
        $this->project->members()->attach($this->user->id, ['role' => ProjectRole::ADMIN->value]);
        $comment = $this->createComment(User::factory()->createOne());

        $this->updateComment($comment)->assertForbidden();

        self::assertSame('Коментар', $comment->refresh()->body);
    }

    public function testAuthorDeletesOwnComment(): void
    {
        $comment = $this->createComment($this->user);

        $this->deleteComment($comment)->assertRedirect();

        $this->assertModelMissing($comment);
    }

    public function testProjectOwnerDeletesForeignComment(): void
    {
        $this->actingAs($this->project->user, 'web');
        $comment = $this->createComment(User::factory()->createOne());

        $this->deleteComment($comment)->assertRedirect();

        $this->assertModelMissing($comment);
    }

    /**
     * @return array<string, array{ProjectRole, bool}>
     */
    public static function memberRoles(): array
    {
        return [
            'admin' => [ProjectRole::ADMIN, true],
            'executor' => [ProjectRole::EXECUTOR, false],
            'client' => [ProjectRole::CLIENT, false],
        ];
    }

    #[DataProvider('memberRoles')]
    public function testMemberRoleDecidesDeletingForeignComment(ProjectRole $role, bool $allowed): void
    {
        $this->project->members()->attach($this->user->id, ['role' => $role->value]);
        $comment = $this->createComment(User::factory()->createOne());

        $response = $this->deleteComment($comment);

        if ($allowed) {
            $response->assertRedirect();
            $this->assertModelMissing($comment);
        } else {
            $response->assertForbidden();
            $this->assertModelExists($comment);
        }
    }

    public function testOutsiderCannotDeleteComment(): void
    {
        $comment = $this->createComment(User::factory()->createOne());

        $this->deleteComment($comment)->assertForbidden();

        $this->assertModelExists($comment);
    }

    public function testCommentFromAnotherTaskIsNotFound(): void
    {
        $comment = $this->createComment($this->user);
        $otherTask = $this->createTask();

        $this->updateComment($comment, $otherTask)->assertNotFound();
        $this->deleteComment($comment, $otherTask)->assertNotFound();

        $this->assertModelExists($comment);
        self::assertSame('Коментар', $comment->refresh()->body);
    }

    private function updateComment(Comment $comment, ?Task $task = null): TestResponse
    {
        return $this->patch(action(UpdateProjectTaskCommentController::class, $this->routeParams($comment, $task)), ['body' => 'Змінено']);
    }

    private function deleteComment(Comment $comment, ?Task $task = null): TestResponse
    {
        return $this->delete(action(DeleteProjectTaskCommentController::class, $this->routeParams($comment, $task)));
    }

    /**
     * @return array<string, int>
     */
    private function routeParams(Comment $comment, ?Task $task): array
    {
        return [
            'project' => $this->project->id,
            'task' => $task->id ?? $comment->model_id,
            'comment' => $comment->id,
        ];
    }

    private function createComment(User $author): Comment
    {
        return Comment::create([
            'user_id' => $author->id,
            'model_type' => Task::class,
            'model_id' => $this->createTask()->id,
            'body' => 'Коментар',
        ]);
    }

    private function createTask(): Task
    {
        return Task::create([
            'user_id' => $this->project->user_id,
            'project_id' => $this->project->id,
            'title' => 'Задача',
            'status' => TaskStatus::TODO->value,
        ]);
    }
}
