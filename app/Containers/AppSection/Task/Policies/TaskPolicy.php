<?php

namespace App\Containers\AppSection\Task\Policies;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Policies\Policy as ParentPolicy;

final class TaskPolicy extends ParentPolicy
{
    /**
     * Project tasks follow the project's rules; project-less tasks belong
     * to their creator and assignee (mirrors TaskRepository::scopeVisibleForUser).
     *
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function update(User $user, Task $task): bool
    {
        if (null !== $task->project) {
            return $user->can('updateTask', $task->project);
        }

        return $task->user_id === $user->id || $task->assignee_id === $user->id;
    }
}
