<?php

namespace App\Containers\AppSection\Comment\UI\CLI\Commands;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Commands\Command as ParentCommand;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;

final class SeedCommentsCommand extends ParentCommand
{
    use ConfirmableTrait;

    protected $signature = 'seed:comments
        {project : Project id whose tasks get the comments}
        {--count=100 : Number of comments to create}
        {--task= : Comment only this task (must belong to the project)}
        {--force : Run in production}';
    protected $description = 'Seed fake comments on tasks of a single project';

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
        $participantIds = $project->participantIds()->all();

        DB::transaction(function () use ($count, $tasks, $participantIds) {
            $this->withProgressBar(range(1, $count), static function () use ($tasks, $participantIds) {
                /** @var Task $task */
                $task = $tasks->random();

                Comment::factory()->forTask($task, fake()->randomElement($participantIds))->createOne();
            });
        });

        $this->newLine();
        $this->info("Created {$count} comments on {$tasks->count()} task(s) of project #{$project->id}.");

        return self::SUCCESS;
    }
}
