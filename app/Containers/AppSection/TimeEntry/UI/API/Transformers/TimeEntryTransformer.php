<?php

namespace App\Containers\AppSection\TimeEntry\UI\API\Transformers;

use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class TimeEntryTransformer extends ParentTransformer
{
    /**
     * @param TimeEntry $timeEntry
     * @return array
     */
    public function transform(TimeEntry $timeEntry): array
    {
        return [
            'id' => $timeEntry->id,
            'description' => $timeEntry->description,
            'started_at' => $timeEntry->started_at->toIso8601String(),
            'stopped_at' => $timeEntry->stopped_at?->toIso8601String(),
            'duration' => $timeEntry->durationInSeconds(),
            'project' => $timeEntry->project ? [
                'id' => $timeEntry->project->id,
                'title' => $timeEntry->project->title,
            ] : null,
        ];
    }
}
