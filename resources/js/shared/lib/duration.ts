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

export function formatTimeOfDay(isoDateTime: string): string {
    return timeOfDayFormatter.format(new Date(isoDateTime));
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
