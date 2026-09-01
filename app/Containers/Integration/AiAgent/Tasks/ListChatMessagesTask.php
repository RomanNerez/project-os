<?php

namespace App\Containers\Integration\AiAgent\Tasks;

use App\Containers\Integration\AiAgent\Data\Repositories\AiChatMessageRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class ListChatMessagesTask extends ParentTask
{
    public function __construct(
        private readonly AiChatMessageRepository $repository
    ) {}

    /**
     * @return mixed
     */
    public function run(): mixed
    {
        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
        $paginator = $this->repository->latest('id')->paginate();

        // Reorder items inside the paginator collection
        return $paginator->setCollection($paginator->getCollection()->reverse()->values());
    }
}