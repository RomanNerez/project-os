<?php

namespace App\Containers\Integration\AiAgent\Data\Repositories;

use App\Containers\Integration\AiAgent\Models\AiChatMessage;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of AiChatMessage
 *
 * @extends ParentRepository<TModel>
 */
final class AiChatMessageRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
