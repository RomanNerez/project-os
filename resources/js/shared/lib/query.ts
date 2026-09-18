export type QueryValue = string | number | null | undefined;

export type Query = Record<string, string>;

export function readQuery(url: string): Query {
    return Object.fromEntries(new URLSearchParams(url.split('?')[1] ?? ''));
}

/**
 * Drops empty values so that defaults never reach the address bar.
 */
export function buildQuery(params: Record<string, QueryValue>): Query {
    const query: Query = {};

    for (const [key, value] of Object.entries(params)) {
        if (value === null || value === undefined || value === '') continue;

        query[key] = String(value);
    }

    return query;
}
