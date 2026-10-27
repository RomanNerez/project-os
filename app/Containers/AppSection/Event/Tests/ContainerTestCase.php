<?php

namespace App\Containers\AppSection\Event\Tests;

use App\Containers\AppSection\Event\Models\Event;
use App\Containers\AppSection\Project\Enums\ProjectStatus;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Tests\TestCase as ParentTestCase;

class ContainerTestCase extends ParentTestCase
{
    protected function createProject(User $owner, string $title = 'Лендінг'): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'title' => $title,
            'status' => ProjectStatus::IN_PROGRESS->value,
            'description' => 'Тестовий проєкт',
            'budget' => 1000,
        ]);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    protected function createEvent(User $author, array $attributes = []): Event
    {
        return Event::create([
            'user_id' => $author->id,
            'title' => 'Дзвінок з клієнтом',
            'type' => 'meeting',
            'start_at' => '2026-10-28 10:00:00',
            'end_at' => '2026-10-28 11:00:00',
            'all_day' => false,
            ...$attributes,
        ]);
    }
}
