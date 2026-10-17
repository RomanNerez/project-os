<?php

namespace App\Containers\AppSection\Comment\Tests\Functional\CLI;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Comment\Tests\FunctionalTestCase;
use App\Containers\AppSection\Comment\UI\CLI\Commands\SeedCommentsCommand;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SeedCommentsCommand::class)]
final class SeedCommentsCommandTest extends FunctionalTestCase
{
    public function testCreatesCommentsOnlyOnTasksOfGivenProject(): void
    {
        $project = Project::factory()->createOne();
        $projectTaskIds = Task::factory()->count(3)->forProject($project)->create()->pluck('id');
        $otherProject = Project::factory()->createOne();
        Task::factory()->count(2)->forProject($otherProject)->create();

        $this->artisan('seed:comments', ['project' => $project->id, '--count' => 25])->assertSuccessful();

        $comments = Comment::query()->get();
        $this->assertCount(25, $comments);

        foreach ($comments as $comment) {
            $this->assertContains($comment->model_id, $projectTaskIds);
            $this->assertSame($project->user_id, $comment->user_id);
        }
    }

    public function testCommentsOnlyGivenTask(): void
    {
        $project = Project::factory()->createOne();
        [$task] = Task::factory()->count(2)->forProject($project)->create();

        $this->artisan('seed:comments', ['project' => $project->id, '--count' => 5, '--task' => $task->id])
            ->assertSuccessful();

        $this->assertSame(5, $task->comments()->count());
        $this->assertSame(5, Comment::query()->count());
    }

    public function testFailsForTaskOfAnotherProject(): void
    {
        $project = Project::factory()->createOne();
        Task::factory()->forProject($project)->createOne();
        $foreignTask = Task::factory()->forProject(Project::factory()->createOne())->createOne();

        $this->artisan('seed:comments', ['project' => $project->id, '--task' => $foreignTask->id])->assertFailed();

        $this->assertSame(0, Comment::query()->count());
    }
}
