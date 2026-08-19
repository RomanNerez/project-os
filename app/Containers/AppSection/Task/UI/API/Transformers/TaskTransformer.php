<?php

namespace App\Containers\AppSection\Task\UI\API\Transformers;

use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class TaskTransformer extends ParentTransformer
{
    /**
     * @param Task $task
     * @return array
     */
    public function transform(Task $task): array
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'project' => $task->project ? [
                'id' => $task->project->id,
                'title' => $task->project->title,
            ] : null,
            'assignee' => $task->assignee ? [
                'id' => $task->assignee->id,
                'name' => $task->assignee->name,
            ] : null,
        ];
    }
}
