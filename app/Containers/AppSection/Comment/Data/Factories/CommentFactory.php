<?php

namespace App\Containers\AppSection\Comment\Data\Factories;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Factories\Factory as ParentFactory;

/**
 * @template TModel of Comment
 *
 * @extends ParentFactory<TModel>
 */
final class CommentFactory extends ParentFactory
{
    /** @var class-string<TModel> */
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'model_type' => (new Task())->getMorphClass(),
            'model_id' => Task::factory(),
            'body' => '<p>' . fake()->sentences(fake()->numberBetween(1, 4), true) . '</p>',
        ];
    }

    public function forTask(Task $task, int $userId): self
    {
        return $this->state(fn (array $attributes) => [
            'model_type' => $task->getMorphClass(),
            'model_id' => $task->id,
            'user_id' => $userId,
        ]);
    }
}
