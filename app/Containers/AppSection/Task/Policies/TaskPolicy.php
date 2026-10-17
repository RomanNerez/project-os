<?php

namespace App\Containers\AppSection\Task\Policies;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Policies\Policy as ParentPolicy;

final class TaskPolicy extends ParentPolicy
{
    /**
     * Task creator or anyone with a role in the task's project.
     *
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function comment(User $user, Task $task): bool
    {
        return $this->isCreator($user, $task) || $task->project->hasParticipant($user);
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function uploadFiles(User $user, Task $task): bool
    {
        return $this->comment($user, $task);
    }

    /**
     * Task creator or anyone who can edit the task (viewers can't).
     *
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function deleteFiles(User $user, Task $task): bool
    {
        return $this->isCreator($user, $task) || $user->can('updateTask', $task->project);
    }

    private function isCreator(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }
}
