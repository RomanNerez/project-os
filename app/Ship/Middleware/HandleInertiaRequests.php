<?php

namespace App\Ship\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        $user = $request->user('web');

        return [
            ...parent::share($request),
            'auth' => [
                'user' => null !== $user ? [
                    ...$user->only(['id', 'name', 'email']),
                    'avatar_url' => $user->getAvatarUrl(),
                    'unread_notifications_count' => $user->unreadNotifications()->count(),
                    'permissions' => [
                        'manage_users' => $user->can('index', [$user::class]),
                    ],
                ] : null,
            ],
        ];
    }
}
