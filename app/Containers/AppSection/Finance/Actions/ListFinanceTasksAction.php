<?php

namespace App\Containers\AppSection\Finance\Actions;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\Tasks\ListUnpaidFinanceTasksTask;
use App\Containers\AppSection\Finance\UI\WEB\Requests\ListFinanceTasksRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Validation\ValidationException;

final class ListFinanceTasksAction extends ParentAction
{
    public function __construct(
        private readonly ListUnpaidFinanceTasksTask $listUnpaidFinanceTasksTask,
    ) {
    }

    /**
     * The edited record must belong to the same project, otherwise its payments are not "freed".
     *
     * @param ListFinanceTasksRequest $request
     * @param int $projectId
     * @return list<array{
     *     task: array{id: int, title: string, project_id: int},
     *     users: list<array{user: User, tracked_seconds: int, paid_seconds: int, hourly_rate: null|string}>
     * }>
     *
     * @throws ValidationException
     */
    public function run(ListFinanceTasksRequest $request, int $projectId): array
    {
        $data = $request->validated();
        $exceptFinanceId = isset($data['except_finance_id']) ? (int) $data['except_finance_id'] : null;

        if (null !== $exceptFinanceId && !Finance::query()->whereKey($exceptFinanceId)->where('project_id', $projectId)->exists()) {
            throw ValidationException::withMessages(['except_finance_id' => 'Запис має належати проєкту.']);
        }

        return $this->listUnpaidFinanceTasksTask->run(
            $projectId,
            isset($data['title']) ? trim((string) $data['title']) : null,
            $exceptFinanceId,
            (int) ($data['limit'] ?? 50),
        );
    }
}
