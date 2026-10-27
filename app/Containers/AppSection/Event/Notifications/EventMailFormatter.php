<?php

namespace App\Containers\AppSection\Event\Notifications;

use App\Containers\AppSection\Event\Models\Event;

final class EventMailFormatter
{
    /**
     * Human-readable period in the display timezone, e.g. "28.10.2026 14:00 – 15:30" or "28.10.2026 (весь день)".
     */
    public static function period(Event $event): string
    {
        if ($event->all_day) {
            $lastDay = $event->end_at?->subDay();
            $days = null !== $lastDay && !$lastDay->isSameDay($event->start_at)
                ? $event->start_at->format('d.m.Y') . ' – ' . $lastDay->format('d.m.Y')
                : $event->start_at->format('d.m.Y');

            return $days . ' (весь день)';
        }

        $timezone = config()->string('appSection-event.display_timezone');
        $start = $event->start_at->setTimezone($timezone);
        $end = $event->end_at?->setTimezone($timezone);

        if (null === $end) {
            return $start->format('d.m.Y H:i');
        }

        return $start->format('d.m.Y H:i') . ' – ' . ($end->isSameDay($start) ? $end->format('H:i') : $end->format('d.m.Y H:i'));
    }
}
