<?php

namespace App\Containers\Integration\AiAgent\UI\API\Transformers;

use App\Containers\Integration\AiAgent\Models\AiChatMessage;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

class AiChatMessageTransformer extends ParentTransformer
{
    /**
     * @param AiChatMessage $item
     * @return array<string, mixed>
     */
    public function transform(AiChatMessage $item): array
    {
        return [
            'id' => $item->id,
            'user_id' => $item->user_id,
            'role' => $item->role->value,
            'content' => $item->content,
        ];
    }
}
