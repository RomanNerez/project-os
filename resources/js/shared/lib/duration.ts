const timeOfDayFormatter = new Intl.DateTimeFormat('uk-UA', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
});

const dayFormatter = new Intl.DateTimeFormat('uk-UA', {
    day: 'numeric',
    month: 'long',
    weekday: 'long',
});

const pad = (value: number): string => String(value).padStart(2, '0');

/** Formats a number of seconds as `HH:MM:SS`, counting hours past 24 instead of wrapping. */
export function formatDuration(seconds: number): string {
    const total = Math.max(0, Math.floor(seconds));

    return [Math.floor(total / 3600), Math.floor((total % 3600) / 60), total % 60]
        .map(pad)
        .join(':');
}

/** Formats a number of minutes as `1 год 30 хв`, `2 год` or `45 хв`. */
export function formatEstimate(minutes: number): string {
    const hours = Math.floor(minutes / 60);
    const rest = minutes % 60;

    return [hours && `${hours} год`, (rest || !hours) && `${rest} хв`].filter(Boolean).join(' ');
}

/** Formats a number of seconds as `1 год 30 хв 15 с`, skipping empty parts. */
export function formatDurationHuman(seconds: number): string {
    const total = Math.max(0, Math.floor(seconds));
    const hours = Math.floor(total / 3600);
    const minutes = Math.floor((total % 3600) / 60);
    const rest = total % 60;

    return [hours && `${hours} год`, minutes && `${minutes} хв`, (rest || total === 0) && `${rest} с`]
        .filter(Boolean)
        .join(' ');
}

export function formatTimeOfDay(isoDateTime: string): string {
    return timeOfDayFormatter.format(new Date(isoDateTime));
}

const dateTimeFormatter = new Intl.DateTimeFormat('uk-UA', {
    day: 'numeric',
    month: 'long',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
});

/** Formats a period as `28 жовтня о 10:00:00 — 12:30:15`, repeating the date only when it ends on another day. */
export function formatDateTimeRange(startIso: string, stopIso: string): string {
    const start = new Date(startIso);
    const stop = new Date(stopIso);
    const end = toDayKey(startIso) === toDayKey(stopIso)
        ? timeOfDayFormatter.format(stop)
        : dateTimeFormatter.format(stop);

    return `${dateTimeFormatter.format(start)} — ${end}`;
}

/** Local `YYYY-MM-DD` key of an ISO datetime, used to group entries into days. */
export function toDayKey(isoDateTime: string): string {
    const date = new Date(isoDateTime);

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}

export function formatDayLabel(dayKey: string): string {
    const [year, month, day] = dayKey.split('-').map(Number);
    const date = new Date(year, month - 1, day);

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const diffInDays = Math.round((today.getTime() - date.getTime()) / 86_400_000);

    if (diffInDays === 0) return 'Сьогодні';
    if (diffInDays === 1) return 'Вчора';

    return dayFormatter.format(date);
}
