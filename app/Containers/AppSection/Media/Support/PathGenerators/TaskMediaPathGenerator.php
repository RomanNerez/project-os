<?php

namespace App\Containers\AppSection\Media\Support\PathGenerators;

use App\Containers\AppSection\Media\Enums\MediaDirectory;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\PathGenerators\MediaPathGenerator as ParentMediaPathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class TaskMediaPathGenerator extends ParentMediaPathGenerator
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

        return $this->join(MediaDirectory::PROJECTS, $task->getAttribute('project_id'), MediaDirectory::TASKS, $task->getKey());
    }
}
