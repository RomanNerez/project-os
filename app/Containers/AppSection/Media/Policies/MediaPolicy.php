<?php

namespace App\Containers\AppSection\Media\Policies;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Policies\Policy as ParentPolicy;

final class MediaPolicy extends ParentPolicy
{
    /**
     * @param User $user
     * @param Media $media
     * @return bool
     */
    public function delete(User $user, Media $media): bool
    {
        $owner = $media->model;

        return $owner instanceof Task && $user->can('deleteFiles', $owner);
    }
}
