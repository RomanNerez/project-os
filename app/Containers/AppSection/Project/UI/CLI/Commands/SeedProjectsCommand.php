<?php

namespace App\Containers\AppSection\Project\UI\CLI\Commands;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Commands\Command as ParentCommand;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;

final class SeedProjectsCommand extends ParentCommand
{
    use ConfirmableTrait;

    protected $signature = 'seed:projects
        {--count=5 : Number of projects to create}
        {--owner= : Owner user id (random existing user by default)}
        {--members=3 : Number of members to add to each project}
        {--force : Run in production}';
    protected $description = 'Seed fake projects with an owner and members';

    public function handle(): int
    {
        if (!$this->confirmToProceed()) {
            return self::FAILURE;
        }

        $count = max(1, (int) $this->option('count'));
        $membersCount = max(0, (int) $this->option('members'));
        $ownerId = $this->option('owner');

        $owner = null;
        if (null !== $ownerId) {
            $owner = User::query()->find($ownerId);

            if (null === $owner) {
                $this->error("User #{$ownerId} not found.");

                return self::FAILURE;
            }
        }

        $projects = DB::transaction(function () use ($count, $membersCount, $owner) {
            $projects = collect();

            for ($i = 0; $i < $count; ++$i) {
                $projectOwner = $owner ?? User::query()->inRandomOrder()->first() ?? User::factory()->createOne();
                $project = Project::factory()->ownedBy($projectOwner)->createOne();

                $this->attachMembers($project, $membersCount);
                $projects->push($project);
            }

            return $projects;
        });

        $this->info('Created projects: ' . $projects->pluck('id')->map(static fn ($id): string => "#{$id}")->implode(', '));

        return self::SUCCESS;
    }

    private function attachMembers(Project $project, int $membersCount): void
    {
        if (0 === $membersCount) {
            return;
        }

        $memberIds = User::query()
            ->whereKeyNot($project->user_id)
            ->inRandomOrder()
            ->limit($membersCount)
            ->pluck('id');

        $missing = $membersCount - $memberIds->count();
        if ($missing > 0) {
            $memberIds = $memberIds->merge(User::factory()->count($missing)->create()->pluck('id'));
        }

        $project->members()->attach(
            $memberIds->mapWithKeys(static fn ($id): array => [
                $id => ['role' => fake()->randomElement(ProjectRole::cases())->value],
            ])->all(),
        );
    }
}
