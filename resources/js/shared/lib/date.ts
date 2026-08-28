const longDateFormatter = new Intl.DateTimeFormat('uk-UA', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

const rtf = new Intl.RelativeTimeFormat('uk-UA', { numeric: 'auto' });

const DIVISORS: { amount: number; name: Intl.RelativeTimeFormatUnit }[] = [
    { amount: 60, name: 'second' },
    { amount: 60, name: 'minute' },
    { amount: 24, name: 'hour' },
    { amount: 7, name: 'day' },
    { amount: 4.34524, name: 'week' },
    { amount: 12, name: 'month' },
    { amount: Number.POSITIVE_INFINITY, name: 'year' },
];

/**
 * Parses a `YYYY-MM-DD` string into a Date at local midnight. Going through
 * `new Date(value)` would treat it as UTC and shift the day in some timezones.
 */
export function parseDateValue(value: string | null): Date | null {
    if (!value) return null;
    
    return new Date(value);
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

export function formatDateHuman(value: string | Date | null | undefined): string | null {
    if (!value) return null;

    const date = typeof value === 'string' ? new Date(value) : value;
    if (isNaN(date.getTime())) return null;

    // Обчислюємо різницю у секундах між датою та поточним часом
    let duration = (date.getTime() - Date.now()) / 1000;

    // Якщо різниця менша 10 секунд — повертаємо "щойно"
    if (Math.abs(duration) < 10) {
        return 'щойно';
    }

    // Підбираємо відповідну одиницю виміру (хвилини, години, дні тощо)
    for (let i = 0; i < DIVISORS.length; i++) {
        const division = DIVISORS[i];
        if (Math.abs(duration) < division.amount) {
            return rtf.format(Math.round(duration), division.name);
        }
        duration /= division.amount;
    }

    return null;
}
