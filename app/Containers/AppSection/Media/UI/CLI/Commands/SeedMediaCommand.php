<?php

namespace App\Containers\AppSection\Media\UI\CLI\Commands;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Commands\Command as ParentCommand;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Http\UploadedFile;

final class SeedMediaCommand extends ParentCommand
{
    use ConfirmableTrait;

    protected $signature = 'seed:media
        {project : Project id whose tasks get the files}
        {--count=20 : Number of files to attach}
        {--task= : Attach only to this task (must belong to the project)}
        {--force : Run in production}';
    protected $description = 'Seed fake files (images and PDFs) on tasks of a single project';

    public function handle(): int
    {
        if (!$this->confirmToProceed()) {
            return self::FAILURE;
        }

        $projectId = $this->argument('project');
        $project = Project::query()->find($projectId);

        if (null === $project) {
            $this->error("Project #{$projectId} not found.");

            return self::FAILURE;
        }

        $tasks = $project->tasks()
            ->when($this->option('task'), static fn ($query, $taskId) => $query->whereKey($taskId))
            ->get();

        if ($tasks->isEmpty()) {
            $this->error("No matching tasks in project #{$project->id}. Run seed:tasks first.");

            return self::FAILURE;
        }

        $count = max(1, (int) $this->option('count'));

        // Files are written to disk, so no DB transaction: a rollback would leave orphaned files.
        $this->withProgressBar(range(1, $count), function () use ($tasks) {
            /** @var Task $task */
            $task = $tasks->random();

            $task->addMedia($this->fakeFile())->toMediaCollection();
        });

        $this->newLine();
        $this->info("Attached {$count} files to {$tasks->count()} task(s) of project #{$project->id}.");

        return self::SUCCESS;
    }

    private function fakeFile(): UploadedFile
    {
        $name = fake()->slug(fake()->numberBetween(1, 3));

        return match (fake()->numberBetween(1, 3)) {
            1 => UploadedFile::fake()->image("{$name}.jpg", fake()->numberBetween(400, 1600), fake()->numberBetween(300, 1200)),
            2 => UploadedFile::fake()->image("{$name}.png", fake()->numberBetween(400, 1600), fake()->numberBetween(300, 1200)),
            default => UploadedFile::fake()->create("{$name}.pdf", fake()->numberBetween(20, 500), 'application/pdf'),
        };
    }
}
