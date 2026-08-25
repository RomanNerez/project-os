<?php

namespace App\Containers\AppSection\Media\Data\Repositories;

use App\Containers\AppSection\Media\Models\Media;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Media
 *
 * @extends ParentRepository<TModel>
 */
final class MediaRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
