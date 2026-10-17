<?php

namespace App\Containers\AppSection\Task\UI\CLI\Commands;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Commands\Command as ParentCommand;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;

final class SeedTasksCommand extends ParentCommand
{
    use ConfirmableTrait;

    protected $signature = 'seed:tasks
        {project : Project id the tasks are created in}
        {--count=50 : Number of tasks to create}
        {--force : Run in production}';
    protected $description = 'Seed fake tasks into a single project';

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

        $count = max(1, (int) $this->option('count'));

        DB::transaction(static fn () => Task::factory()->count($count)->forProject($project)->create());

        $this->info("Created {$count} tasks in project #{$project->id}.");

        return self::SUCCESS;
    }
}
