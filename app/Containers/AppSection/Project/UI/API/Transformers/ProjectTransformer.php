<?php

namespace App\Containers\AppSection\Project\UI\API\Transformers;

use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class ProjectTransformer extends ParentTransformer
{
    /**
     * @param Project $project
     * @return array
     */
    public function transform(Project $project): array
    {
        return [
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'status' => $project->status,
            'budget' => $project->budget,
            'active_until' => $project->active_until?->toDateString(),
        ];
    }
}
