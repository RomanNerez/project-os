<?php

namespace App\Containers\AppSection\Media\UI\API\Transformers;

use App\Containers\AppSection\Media\Models\Media;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class MediaTransformer extends ParentTransformer
{
    /**
     * @param Media $item
     * @return array
     */
    public function transform(Media $item): array
    {
        return [
            'id' => $item->id,
            'file_name' => $item->file_name,
            'size' => $item->size,
            'mime_type' => $item->mime_type,
            'origin_url' => $item->getUrl(),
            'preview_url' => $item->getUrl('preview'),
        ];
    }
}
