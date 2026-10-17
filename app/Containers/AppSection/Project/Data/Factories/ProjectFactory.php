<?php

namespace App\Containers\AppSection\Project\Data\Factories;

use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Factories\Factory as ParentFactory;

/**
 * @template TModel of Project
 *
 * @extends ParentFactory<TModel>
 */
final class ProjectFactory extends ParentFactory
{
    /** @var class-string<TModel> */
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->catchPhrase(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(ProjectStatus::cases())->value,
            'budget' => fake()->randomFloat(2, 1000, 100000),
            'active_until' => fake()->optional(0.7)->dateTimeBetween('now', '+1 year'),
        ];
    }

    public function ownedBy(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
