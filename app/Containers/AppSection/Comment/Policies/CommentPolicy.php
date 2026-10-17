<?php

namespace App\Containers\AppSection\Comment\Policies;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Policies\Policy as ParentPolicy;

final class CommentPolicy extends ParentPolicy
{
    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function update(User $user, Comment $comment): bool
    {
        return $this->isAuthor($user, $comment);
    }

    /**
     * Author, or the owner/admin of the commented task's project.
     *
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($this->isAuthor($user, $comment)) {
            return true;
        }

        $task = $comment->model;

        return $task instanceof Task && $user->can('update', $task->project);
    }

    private function isAuthor(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }
}
