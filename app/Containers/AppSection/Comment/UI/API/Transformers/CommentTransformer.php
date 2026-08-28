<?php

namespace App\Containers\AppSection\Comment\UI\API\Transformers;

use App\Containers\AppSection\Comment\Models\Comment;
use App\Containers\AppSection\User\UI\API\Transformers\UserTransformer;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class CommentTransformer extends ParentTransformer
{
    /**
     * @var array<int, string>
     */
    protected array $availableIncludes = [
        'user',
    ];

    /**
     * @param Comment $item
     * @return array
     */
    public function transform(Comment $item): array
    {
        return [
            'id' => $item->id,
            'user_id' => $item->user_id,
            'body' => $item->body,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
    }

    /**
     * @param Comment $item
     * @return array
     */
    public function includeUser(Comment $item)
    {
        return $this->item($item->user, new UserTransformer());
    }
}
