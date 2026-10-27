<?php

namespace App\Containers\AppSection\Media\Support\PathGenerators;

use App\Containers\AppSection\Finance\Models\Finance;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class FinanceMediaPathGenerator extends ProjectMediaPathGenerator
{
    /**
     * @param Media $media
     * @return string
     */
    protected function getDirectory(Media $media): string
    {
        $finance = $this->getOwner($media);

        if (!$finance instanceof Finance) {
            throw new \UnexpectedValueException('Media ' . $this->segment($media->getKey()) . ' does not belong to a finance record.');
        }

        return 'projects/' . $this->segment($finance->getAttribute('project_id')) . '/finance/' . $this->segment($finance->getKey());
    }
}
