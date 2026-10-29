<?php

namespace App\Ship\Parents\PathGenerators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Stores media under a directory derived from the owning model: {prefix}/{directory}/{media_id}/.
 * The media id segment keeps files with the same name (and their conversions) apart.
 */
abstract class MediaPathGenerator implements PathGenerator
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
     * Joins path segments with "/"; backed enums contribute their value, anything else must be an id.
     *
     * @param mixed ...$segments
     * @return string
     */
    protected function join(mixed ...$segments): string
    {
        return implode('/', array_map(
            fn (mixed $segment): string => $segment instanceof \BackedEnum ? (string) $segment->value : $this->segment($segment),
            $segments,
        ));
    }

    /**
     * @param Media $media
     * @return string
     */
    protected function getBasePath(Media $media): string
    {
        return $this->withPrefix($this->join($this->getDirectory($media), $media->getKey()));
    }

    /**
     * @param string $path
     * @return string
     */
    protected function withPrefix(string $path): string
    {
        $prefix = config('media-library.prefix', '');

        return \is_string($prefix) && '' !== $prefix ? $this->join($prefix, $path) : $path;
    }
}
