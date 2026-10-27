import type { IncludedData } from '@/shared/types';

export const EVENT_TYPE = {
    MEETING: 'meeting',
    DEADLINE: 'deadline',
    REMINDER: 'reminder',
    PERSONAL: 'personal',
    OTHER: 'other',
} as const;

export const PARTICIPANT_STATUS = {
    PENDING: 'pending',
    ACCEPTED: 'accepted',
    DECLINED: 'declined',
} as const;

export type EventType = typeof EVENT_TYPE[keyof typeof EVENT_TYPE];
export type ParticipantStatus = typeof PARTICIPANT_STATUS[keyof typeof PARTICIPANT_STATUS];
export type EventID = number;

type Severity = 'secondary' | 'info' | 'warn' | 'success' | 'danger';

/** `color` is used when the event has no color of its own. */
export const TYPE_META: Record<EventType, { label: string; color: string; icon: string }> = {
    meeting: { label: 'Зустріч', color: '#3b82f6', icon: 'pi pi-users' },
    deadline: { label: 'Дедлайн', color: '#ef4444', icon: 'pi pi-flag' },
    reminder: { label: 'Нагадування', color: '#f59e0b', icon: 'pi pi-bell' },
    personal: { label: 'Особисте', color: '#10b981', icon: 'pi pi-user' },
    other: { label: 'Інше', color: '#64748b', icon: 'pi pi-calendar' },
};

export const PARTICIPANT_STATUS_META: Record<ParticipantStatus, { label: string; severity: Severity }> = {
    pending: { label: 'Очікує відповіді', severity: 'secondary' },
    accepted: { label: 'Прийнято', severity: 'success' },
    declined: { label: 'Відхилено', severity: 'danger' },
};

export const TYPE_OPTIONS = (Object.keys(TYPE_META) as EventType[])
    .map((value) => ({ value, label: TYPE_META[value].label }));

export const REMINDER_OPTIONS: { value: number | null; label: string }[] = [
    { value: null, label: 'Без нагадування' },
    { value: 0, label: 'У момент початку' },
    { value: 5, label: 'За 5 хвилин' },
    { value: 15, label: 'За 15 хвилин' },
    { value: 30, label: 'За 30 хвилин' },
    { value: 60, label: 'За 1 годину' },
    { value: 1440, label: 'За 1 день' },
];

export interface EventParticipant {
    id: number;
    name: string;
    avatar_url: string | null;
    status: ParticipantStatus;
}

/**
 * EventTransformer with participants. All-day events have `YYYY-MM-DD` dates with an exclusive end,
 * timed ones ISO 8601 instants.
 */
export interface CalendarEvent {
    id: EventID;
    user_id: number;
    project_id: number | null;
    project: { id: number; title: string } | null;
    title: string;
    description: string | null;
    location: string | null;
    type: EventType;
    color: string | null;
    start_at: string;
    end_at: string | null;
    all_day: boolean;
    reminder_minutes: number | null;
    /** The current user's answer; null when they are not invited. */
    my_status: ParticipantStatus | null;
    permissions: { update: boolean; delete: boolean };
    participants: IncludedData<EventParticipant[]>;
}

/** A user who can be invited (`calendar.participants.index`). */
export interface ParticipantOption {
    id: number;
    name: string;
    avatar_url: string | null;
}

/** A period as FullCalendar reports it: all-day `end` is exclusive. */
export interface EventPeriod {
    start: Date;
    end: Date | null;
    allDay: boolean;
}

/** All-day `end_at` here is the last day itself (inclusive), as the user picks it. */
export interface EventDraft {
    project_id: number | null;
    title: string;
    type: EventType;
    color: string | null;
    all_day: boolean;
    start_at: Date | null;
    end_at: Date | null;
    location: string;
    description: string;
    reminder_minutes: number | null;
    participant_ids: number[];
}

export const eventColor = (event: Pick<CalendarEvent, 'color' | 'type'>): string => event.color ?? TYPE_META[event.type].color;
