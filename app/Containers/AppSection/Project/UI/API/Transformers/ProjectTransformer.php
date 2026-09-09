<?php

namespace App\Containers\AppSection\Project\UI\API\Transformers;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Containers\AppSection\User\UI\API\Transformers\UserTransformer;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class ProjectTransformer extends ParentTransformer
{
    /**
     * @var array<int, string>
     */
    protected array $availableIncludes = [
        'user',
        'members',
        'task_status_counts',
    ];

    /**
     * @param Project $item
     * @return array
     */
    public function transform(Project $item): array
    {
        return [
            'id' => $item->id,
            'user_id' => $item->user_id,
            'title' => $item->title,
            'description' => $item->description,
            'status' => $item->status,
            'budget' => $item->budget,
            'active_until' => $item->active_until?->toDateString(),
        ];
    }

    /**
     * @param Project $item
     * @return array
     */
    public function includeUser(Project $item)
    {
        return $this->item($item->user, new UserTransformer());
    }

    /**
     * @param Project $item
     * @return array
     */
    public function includeMembers(Project $item)
    {
        return $this->collection($item->members, new ProjectMemberTransformer());
    }

    /**
     * @param Project $item
     * @return array
     */
    public function includeTaskStatusCounts(Project $item)
    {
        return $this->primitive([
            'total' => $item->tasks_count ?? 0,
            TaskStatus::DONE->value => $item->done_tasks_count ?? 0,
        ]);
    }
}
