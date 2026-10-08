<?php

namespace App\Containers\AppSection\Project\Models;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Project extends ParentModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'budget',
        'active_until',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'active_until' => 'immutable_date',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->using(ProjectUser::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * @param User $user
     * @return ProjectRole|null
     */
    public function memberRole(User $user): ProjectRole|null
    {
        $member = $this->relationLoaded('members')
            ? $this->members->firstWhere('id', $user->id)
            : $this->members()->whereKey($user->id)->first();

        return null === $member ? null : ProjectRole::tryFrom($member->pivot->role);
    }
}
