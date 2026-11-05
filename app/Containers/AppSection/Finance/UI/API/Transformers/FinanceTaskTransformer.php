<?php

namespace App\Containers\AppSection\Finance\UI\API\Transformers;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class FinanceTaskTransformer extends ParentTransformer
{
    /**
     * @param Finance $item
     * @return array
     */
    public function transform($item): array
    {
        return [
            'task_id' => $item->pivot->task_id,
            'finance_id' => $item->pivot->finance_id,
            'user_id' => $item->pivot->user_id,
            'seconds' => $item->pivot->seconds,
            'hourly_rate' => $item->pivot->hourly_rate,
        ];
    }
}
