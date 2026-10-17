<?php

namespace App\Containers\AppSection\Task\Data\Factories;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Factories\Factory as ParentFactory;

/**
 * @template TModel of Task
 *
 * @extends ParentFactory<TModel>
 */
final class TaskFactory extends ParentFactory
{
    /** @var class-string<TModel> */
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'assignee_id' => null,
            'title' => rtrim(fake()->sentence(fake()->numberBetween(3, 8)), '.'),
            'description' => fake()->boolean(80)
                ? collect(fake()->paragraphs(fake()->numberBetween(1, 3)))
                    ->map(static fn (string $paragraph): string => "<p>{$paragraph}</p>")
                    ->implode('')
                : null,
            'status' => fake()->randomElement(TaskStatus::cases())->value,
        ];
    }

    /**
     * Places the task in the project with a random participant as author and (optionally) assignee.
     */
    public function forProject(Project $project): self
    {
        $participantIds = $project->participantIds()->all();

        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
            'user_id' => fake()->randomElement($participantIds),
            'assignee_id' => fake()->optional(0.7)->randomElement($participantIds),
        ]);
    }
}
