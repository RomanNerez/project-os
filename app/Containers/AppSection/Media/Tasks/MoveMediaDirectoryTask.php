<?php

namespace App\Containers\AppSection\Media\Tasks;

use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory;

/**
 * Path generators derive the directory from the owning model, so when the model moves
 * (e.g. a finance record to another project) the stored files have to follow it.
 */
final class MoveMediaDirectoryTask extends ParentTask
{
    /**
     * @param Media $media
     * @param string $oldDirectory directory returned by the path generator before the owner changed
     * @return void
     */
    public function run(Media $media, string $oldDirectory): void
    {
        $newDirectory = PathGeneratorFactory::create($media)->getPath($media);

        if ($newDirectory === $oldDirectory) {
            return;
        }

        $diskNames = array_filter([$media->disk, $media->conversions_disk], static fn (string|null $name): bool => null !== $name && '' !== $name);

        foreach (array_unique($diskNames) as $diskName) {
            $disk = Storage::disk($diskName);

            foreach ($disk->allFiles($oldDirectory) as $file) {
                if (\is_string($file)) {
                    $disk->move($file, $newDirectory . substr($file, \strlen($oldDirectory)));
                }
            }

            $disk->deleteDirectory($oldDirectory);
        }
    }
}
