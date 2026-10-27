<?php

namespace App\Containers\AppSection\Event\UI\WEB\Controllers;

use App\Containers\AppSection\Event\Actions\ListEventParticipantCandidatesAction;
use App\Containers\AppSection\Event\UI\WEB\Requests\ListEventParticipantCandidatesRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\JsonResponse;

final class ListEventParticipantCandidatesController extends WebController
{
    public function __construct(
        private readonly ListEventParticipantCandidatesAction $action,
    ) {
    }

    public function __invoke(ListEventParticipantCandidatesRequest $request): JsonResponse
    {
        $projectId = $request->filled('project_id') ? $request->integer('project_id') : null;
        $users = $this->action->run($request->user('web')->id, $projectId);

        return response()->json([
            'data' => $users->map(static fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->getAvatarUrl(),
            ])->all(),
        ]);
    }
}
