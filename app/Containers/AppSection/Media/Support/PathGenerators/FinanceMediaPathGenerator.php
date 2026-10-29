<?php

namespace App\Containers\AppSection\Media\Support\PathGenerators;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Media\Enums\MediaDirectory;
use App\Ship\Parents\PathGenerators\MediaPathGenerator as ParentMediaPathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class FinanceMediaPathGenerator extends ParentMediaPathGenerator
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

        return $this->join(MediaDirectory::PROJECTS, $finance->getAttribute('project_id'), MediaDirectory::FINANCE, $finance->getKey());
    }
}
