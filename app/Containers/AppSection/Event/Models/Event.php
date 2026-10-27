<?php

namespace App\Containers\AppSection\Event\Models;

use App\Containers\AppSection\Event\Enums\EventParticipantStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Models\Model as ParentModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A calendar event: personal when `project_id` is null, otherwise shared with the project.
 * All-day events keep FullCalendar's exclusive end date (midnight of the day after).
 *
 * @property CarbonImmutable $start_at
 * @property CarbonImmutable|null $end_at
 */
final class Event extends ParentModel
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'description',
        'location',
        'type',
        'color',
        'start_at',
        'end_at',
        'all_day',
        'reminder_minutes',
        'reminded_at',
    ];

    protected $casts = [
        'start_at' => 'immutable_datetime',
        'end_at' => 'immutable_datetime',
        'all_day' => 'boolean',
        'reminder_minutes' => 'integer',
        'reminded_at' => 'immutable_datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Invited users with their answer in `pivot->status`; the author is not one of them.
     *
     * @return BelongsToMany<User, $this>
     */
    public function participants(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->withPivot(['status'])
            ->withTimestamps();
    }

    /**
     * Events the user created, was invited to, or that belong to a project they participate in.
     *
     * @param Builder<Event> $query
     */
    public function scopeVisibleTo(Builder $query, int $userId): void
    {
        $query->where(function (Builder $q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhereHas('participants', fn ($participantQuery) => $participantQuery->where('users.id', $userId))
                ->orWhereHas('project', fn ($projectQuery) => $projectQuery->visibleTo($userId));
        });
    }

    /**
     * Events that overlap the [$start, $end) range; an event without an end lasts an instant.
     *
     * @param Builder<Event> $query
     */
    public function scopeOverlapping(Builder $query, CarbonImmutable $start, CarbonImmutable $end): void
    {
        $query->where('start_at', '<', $end)
            ->whereRaw('coalesce(end_at, start_at) >= ?', [$start]);
    }

    public function isAuthoredBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * @return EventParticipantStatus|null null when the user is not invited
     */
    public function participantStatus(User $user): EventParticipantStatus|null
    {
        $participant = $this->relationLoaded('participants')
            ? $this->participants->firstWhere('id', $user->id)
            : $this->participants()->whereKey($user->id)->first();

        return null === $participant ? null : EventParticipantStatus::tryFrom($participant->pivot->status);
    }
}
