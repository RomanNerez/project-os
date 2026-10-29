<?php

namespace App\Containers\AppSection\Media\Support\PathGenerators;

use App\Containers\AppSection\Media\Enums\MediaDirectory;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\PathGenerators\MediaPathGenerator as ParentMediaPathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Stores user media as {prefix}/users/{user_id}/{collection}/, without a media id segment:
 * the avatar collection holds a single file, so the directory never mixes several media.
 */
final class UserMediaPathGenerator extends ParentMediaPathGenerator
{
    /**
     * @param Media $media
     * @return string
     */
    protected function getDirectory(Media $media): string
    {
        $user = $this->getOwner($media);

        if (!$user instanceof User) {
            throw new \UnexpectedValueException('Media ' . $this->segment($media->getKey()) . ' does not belong to a user.');
        }

        return $this->join(MediaDirectory::USERS, $user->getKey(), $media->collection_name);
    }

    /**
     * @param Media $media
     * @return string
     */
    protected function getBasePath(Media $media): string
    {
        return $this->withPrefix($this->getDirectory($media));
    }
}
