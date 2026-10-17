<?php

namespace App\Containers\AppSection\Media\Tests\Functional\CLI;

use App\Containers\AppSection\Media\Tests\FunctionalTestCase;
use App\Containers\AppSection\Media\UI\CLI\Commands\SeedMediaCommand;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[CoversClass(SeedMediaCommand::class)]
final class SeedMediaCommandTest extends FunctionalTestCase
{
    public function testAttachesFilesOnlyToTasksOfGivenProject(): void
    {
        Storage::fake(config('media-library.disk_name'));

        $project = Project::factory()->createOne();
        $projectTaskIds = Task::factory()->count(2)->forProject($project)->create()->pluck('id');
        Task::factory()->forProject(Project::factory()->createOne())->createOne();

        $this->artisan('seed:media', ['project' => $project->id, '--count' => 6])->assertSuccessful();

        $media = Media::query()->get();
        $this->assertCount(6, $media);

        foreach ($media as $item) {
            $this->assertContains($item->model_id, $projectTaskIds);
        }
    }

    public function testFailsWhenProjectHasNoTasks(): void
    {
        $project = Project::factory()->createOne();

        $this->artisan('seed:media', ['project' => $project->id])->assertFailed();
    }
}
