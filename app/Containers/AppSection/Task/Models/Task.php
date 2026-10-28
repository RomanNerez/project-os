<?php

namespace App\Containers\AppSection\Task\Models;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class Task extends ParentModel implements HasMedia
{
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'assignee_id',
        'title',
        'description',
        'status',
        'priority',
        'estimate',
    ];

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'model');
    }

    /**
     * @return BelongsToMany<Finance, $this>
     */
    public function finances(): BelongsToMany
    {
        return $this->belongsToMany(Finance::class, 'finance_task');
    }

    /**
     * Tasks in projects the user owns or is a member of, plus project-less tasks they created or are assigned.
     *
     * @param Builder<Task> $query
     * @param int $userId
     * @return void
     */
    public function scopeVisibleTo(Builder $query, int $userId): void
    {
        $query->where(function (Builder $q) use ($userId) {
            $q->whereHas('project', function ($projectQuery) use ($userId) {
                $projectQuery->where('user_id', $userId)
                    ->orWhereHas('members', function ($memberQuery) use ($userId) {
                        $memberQuery->where('users.id', $userId);
                    });
            })

            ->orWhere(function ($noProjectQuery) use ($userId) {
                $noProjectQuery->whereNull('project_id')
                    ->where(function ($userQuery) use ($userId) {
                        $userQuery->where('user_id', $userId)
                                ->orWhere('assignee_id', $userId);
                    });
            });
        });
    }

    /**
     * @param null|Media $media
     * @return void
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
}
