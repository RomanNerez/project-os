<?php

namespace App\Containers\AppSection\Notification\Tests;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Tests\TestCase as ParentTestCase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class ContainerTestCase extends ParentTestCase
{
    /**
     * @param array<string, mixed> $data
     */
    protected function createNotification(User $user, array $data = [], bool $read = false): DatabaseNotification
    {
        return DatabaseNotification::query()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => $user->getMorphClass(),
            'notifiable_id' => $user->getKey(),
            'data' => [
                'type' => 'test',
                'title' => 'Тестова нотифікація',
                'message' => 'Текст нотифікації',
                'url' => '/calendar',
                ...$data,
            ],
            'read_at' => $read ? now() : null,
        ]);
    }
}
