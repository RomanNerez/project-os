<?php

namespace App\Containers\AppSection\Media\Support\PathGenerators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Stores media under the owning project: {prefix}/projects/{project_id}/.../{media_id}/.
 * The media id segment keeps files with the same name (and their conversions) apart.
 */
abstract class ProjectMediaPathGenerator implements PathGenerator
{
    /**
     * @param Media $media
     * @return string
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /**
     * @param Media $media
     * @return string
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /**
     * @param Media $media
     * @return string
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    /**
     * Directory of the owning model, relative to the prefix, without a trailing slash.
     *
     * @param Media $media
     * @return string
     */
    abstract protected function getDirectory(Media $media): string;

    /**
     * Lazy loading is disabled app-wide, and spatie does not always set the owner relation.
     *
     * @param Media $media
     * @return mixed
     */
    protected function getOwner(Media $media): mixed
    {
        return $media->loadMissing('model')->getRelation('model');
    }

    /**
     * Ids come from untyped model attributes; anything but an int or string would build a broken path.
     *
     * @param mixed $value
     * @return string
     */
    protected function segment(mixed $value): string
    {
        if (!\is_int($value) && !\is_string($value)) {
            throw new \UnexpectedValueException('Media path segment must be an id.');
        }

        return (string) $value;
    }

    /**
     * @param Media $media
     * @return string
     */
    private function getBasePath(Media $media): string
    {
        $prefix = config('media-library.prefix', '');
        $path = $this->getDirectory($media) . '/' . $this->segment($media->getKey());

        return \is_string($prefix) && '' !== $prefix ? $prefix . '/' . $path : $path;
    }
}
