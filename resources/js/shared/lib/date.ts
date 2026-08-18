const longDateFormatter = new Intl.DateTimeFormat('uk-UA', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

/**
 * Parses a `YYYY-MM-DD` string into a Date at local midnight. Going through
 * `new Date(value)` would treat it as UTC and shift the day in some timezones.
 */
export function parseDateValue(value: string | null): Date | null {
    if (!value) return null;

    const [year, month, day] = value.split('-').map(Number);

    return new Date(year, month - 1, day);
}

export function toDateValue(date: Date | null): string | null {
    if (!date) return null;

    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${date.getFullYear()}-${month}-${day}`;
}

export function formatDateLong(value: string | null): string | null {
    const date = parseDateValue(value);

    return date ? longDateFormatter.format(date) : null;
}
