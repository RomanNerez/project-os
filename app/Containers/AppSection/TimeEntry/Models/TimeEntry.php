<?php

namespace App\Containers\AppSection\TimeEntry\Models;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TimeEntry extends ParentModel
{
    protected $fillable = [
        'user_id',
        'project_id',
        'description',
        'started_at',
        'stopped_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isRunning(): bool
    {
        return null === $this->stopped_at;
    }

    public function durationInSeconds(): ?int
    {
        if (null === $this->stopped_at) {
            return null;
        }

        return $this->stopped_at->getTimestamp() - $this->started_at->getTimestamp();
    }
}
