<?php

namespace App\Containers\AppSection\Notification\UI\WEB\Controllers;

use App\Containers\AppSection\Notification\Actions\MarkAllNotificationsAsReadAction;
use App\Containers\AppSection\Notification\UI\WEB\Requests\MarkAllNotificationsAsReadRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\JsonResponse;

final class MarkAllNotificationsAsReadController extends WebController
{
    public function __construct(
        private readonly MarkAllNotificationsAsReadAction $action,
    ) {
    }

    public function __invoke(MarkAllNotificationsAsReadRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user('web');
        $this->action->run($user);

        return response()->json(['unread_count' => $user->unreadNotifications()->count()]);
    }
}
