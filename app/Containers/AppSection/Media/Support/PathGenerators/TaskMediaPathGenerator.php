<?php

namespace App\Containers\AppSection\Media\Support\PathGenerators;

use App\Containers\AppSection\Task\Models\Task;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class TaskMediaPathGenerator extends ProjectMediaPathGenerator
{
    /**
     * @param Media $media
     * @return string
     */
    protected function getDirectory(Media $media): string
    {
        $task = $this->getOwner($media);

        if (!$task instanceof Task) {
            throw new \UnexpectedValueException('Media ' . $this->segment($media->getKey()) . ' does not belong to a task.');
        }

        return 'projects/' . $this->segment($task->getAttribute('project_id')) . '/tasks/' . $this->segment($task->getKey());
    }
}
