<?php

namespace App\Containers\AppSection\Event\UI\CLI\Commands;

use App\Containers\AppSection\Event\Actions\SendEventRemindersAction;
use App\Ship\Parents\Commands\Command as ParentCommand;

final class SendEventRemindersCommand extends ParentCommand
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Email reminders for upcoming calendar events (runs every minute from the scheduler)';

    public function handle(SendEventRemindersAction $action): int
    {
        $this->info('Reminded events: ' . $action->run());

        return self::SUCCESS;
    }
}
