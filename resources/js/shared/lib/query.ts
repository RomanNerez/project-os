export type QueryValue = string | number | null | undefined;

export type Query = Record<string, string>;

export function readQuery(url: string): Query {
    return Object.fromEntries(new URLSearchParams(url.split('?')[1] ?? ''));
}

export function excludeQueryParams(params: Query, keys: string[]): Query {
    const keysToExclude = new Set(keys);
    const result: Query = {};

    for (const key of Object.keys(params)) {
        if (!keysToExclude.has(key)) {
            result[key] = params[key];
        }
    }

    return result;
}

export function buildQuery(params: Record<string, QueryValue>): Query {
    const query: Query = {};

    for (const [key, value] of Object.entries(params)) {
        if (value === null || value === undefined || value === '') continue;

        query[key] = String(value);
    }

    return query;
}
