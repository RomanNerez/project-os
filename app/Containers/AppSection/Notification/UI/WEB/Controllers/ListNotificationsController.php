<?php

namespace App\Containers\AppSection\Notification\UI\WEB\Controllers;

use App\Containers\AppSection\Notification\Actions\ListNotificationsAction;
use App\Containers\AppSection\Notification\UI\WEB\Requests\ListNotificationsRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;

final class ListNotificationsController extends WebController
{
    public function __construct(
        private readonly ListNotificationsAction $action,
    ) {
    }

    public function __invoke(ListNotificationsRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user('web');
        $notifications = $this->action->run($user);

        return response()->json([
            'data' => collect($notifications->items())->map(static fn (DatabaseNotification $notification): array => [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? class_basename($notification->type),
                'title' => $notification->data['title'] ?? '',
                'message' => $notification->data['message'] ?? '',
                'url' => $notification->data['url'] ?? null,
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at' => $notification->created_at?->toIso8601String(),
            ])->all(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'unread_count' => $user->unreadNotifications()->count(),
            ],
        ]);
    }
}
