import { parseDateValue, toDateValue } from '@/shared/lib';
import type { CalendarEvent, EventDraft, EventPeriod } from '../model/types';

const addDays = (date: Date, days: number): Date => new Date(date.getFullYear(), date.getMonth(), date.getDate() + days);

/** Next round hour, one hour long: the default period of a new event. */
function defaultPeriod(): EventPeriod {
    const start = new Date();
    start.setHours(start.getHours() + 1, 0, 0, 0);

    return { start, end: new Date(start.getTime() + 60 * 60 * 1000), allDay: false };
}

/** A new event's draft; a FullCalendar all-day period's exclusive end becomes the inclusive last day. */
export function emptyEventDraft(period: EventPeriod | null = null, projectId: number | null = null): EventDraft {
    const { start, end, allDay } = period ?? defaultPeriod();

    return {
        project_id: projectId,
        title: '',
        type: 'meeting',
        color: null,
        all_day: allDay,
        start_at: start,
        end_at: allDay && end ? addDays(end, -1) : end,
        location: '',
        description: '',
        reminder_minutes: allDay ? null : 15,
        participant_ids: [],
    };
}

export function eventToDraft(event: CalendarEvent): EventDraft {
    const parse = (value: string | null): Date | null => {
        if (!value) return null;

        return event.all_day ? parseDateValue(value) : new Date(value);
    };
    const end = parse(event.end_at);

    return {
        project_id: event.project_id,
        title: event.title,
        type: event.type,
        color: event.color,
        all_day: event.all_day,
        start_at: parse(event.start_at),
        end_at: event.all_day && end ? addDays(end, -1) : end,
        location: event.location ?? '',
        description: event.description ?? '',
        reminder_minutes: event.reminder_minutes,
        participant_ids: event.participants.data.map((participant) => participant.id),
    };
}

/**
 * The server's period format: all-day events as local dates with an exclusive end,
 * timed ones as UTC instants.
 */
export function toPeriodPayload(start: Date | null, end: Date | null, allDay: boolean, endIsInclusive: boolean) {
    if (allDay) {
        return {
            all_day: true,
            start_at: toDateValue(start),
            end_at: end ? toDateValue(endIsInclusive ? addDays(end, 1) : end) : null,
        };
    }

    return {
        all_day: false,
        start_at: start?.toISOString() ?? null,
        end_at: end?.toISOString() ?? null,
    };
}
