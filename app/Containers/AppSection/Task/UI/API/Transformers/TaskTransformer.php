<?php

namespace App\Containers\AppSection\Task\UI\API\Transformers;

use App\Containers\AppSection\Comment\UI\API\Transformers\CommentTransformer;
use App\Containers\AppSection\Media\UI\API\Transformers\MediaTransformer;
use App\Containers\AppSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\UI\API\Transformers\UserTransformer;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class TaskTransformer extends ParentTransformer
{
    /**
     * @var array<int, string>
     */
    protected array $defaultIncludes = [];

    /**
     * @var array<int, string>
     */
    protected array $availableIncludes = [
        'project',
        'assignee',
        'comments',
        'media',
    ];

    /**
     * @param Task $item
     * @return array
     */
    public function transform(Task $item): array
    {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'description' => $item->description,
            'status' => $item->status,
            'project_id' => $item->project_id,
            'assignee_id' => $item->assignee_id,
        ];
    }

    /**
     * @param Task $task
     * @return array
     */
    public function includeProject(Task $task)
    {
        return $this->nullableItem($task->project, new ProjectTransformer());
    }

    /**
     * @param Task $task
     * @return array
     */
    public function includeAssignee(Task $task)
    {
        return $this->nullableItem($task->assignee, new UserTransformer());
    }

    /**
     * @param Task $task
     * @return array
     */
    public function includeMedia(Task $task)
    {
        return $this->collection($task->getMedia(), new MediaTransformer());
    }

    /**
     * @param Task $task
     * @return array
     */
    public function includeComments(Task $task)
    {
        return $this->collection($task->comments, new CommentTransformer());
    }
}
