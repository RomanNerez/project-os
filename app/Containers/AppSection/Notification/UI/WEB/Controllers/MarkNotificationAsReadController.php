<?php

namespace App\Containers\AppSection\Notification\UI\WEB\Controllers;

use App\Containers\AppSection\Notification\Actions\MarkNotificationAsReadAction;
use App\Containers\AppSection\Notification\UI\WEB\Requests\MarkNotificationAsReadRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\JsonResponse;

final class MarkNotificationAsReadController extends WebController
{
    public function __construct(
        private readonly MarkNotificationAsReadAction $action,
    ) {
    }

    public function __invoke(MarkNotificationAsReadRequest $request, string $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user('web');
        $this->action->run($user, $id);

        return response()->json(['unread_count' => $user->unreadNotifications()->count()]);
    }
}
