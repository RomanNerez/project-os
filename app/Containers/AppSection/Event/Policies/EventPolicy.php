<?php

namespace App\Containers\AppSection\Event\Policies;

use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Project\Policies\BasePolicy;
use App\Containers\AppSection\User\Models\User;

final class EventPolicy extends BasePolicy
{
    public function view(User $user, Event $event): bool
    {
        return $event->isAuthoredBy($user)
            || null !== $event->participantStatus($user)
            || (null !== $event->project && $event->project->hasParticipant($user));
    }

    /**
     * The author edits the event; in a project its owner and admins can too.
     */
    public function update(User $user, Event $event): bool
    {
        return $event->isAuthoredBy($user)
            || (null !== $event->project && $this->isOwnerOrAdmin($user, $event->project));
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    /**
     * Only invited users accept or decline.
     */
    public function respond(User $user, Event $event): bool
    {
        return null !== $event->participantStatus($user);
    }
}
