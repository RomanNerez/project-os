export type FilterQuery = Record<string, string>;

export const FIELD_SEPARATOR = ';';
export const FIELD_VALUE_SEPARATOR = ':';
export const VALUE_SEPARATOR = ',';
export const SEARCH_JOIN = {
    AND: 'and',
    OR: 'or',
} as const;

const RESERVED = /[:;%_]/g;

export function sanitizeSearchText(value: string): string {
    return value.replace(RESERVED, '').trim();
}

export function parseSearchParam(url: string): FilterQuery {
    const query = new URLSearchParams(url.split('?')[1] ?? '');
    const keyValues = (query.get('search') ?? '').split(FIELD_SEPARATOR);
    const params: FilterQuery = {};
    
    for (const part of keyValues) {
        const separator = part.indexOf(FIELD_VALUE_SEPARATOR);

        if (separator === -1) continue;

        const field = part.slice(0, separator);
        const value = part.slice(separator + 1);

        params[field] = value;
    }

    return params;
}

export function parseArrayField(value: string): string[] {
    if (!value) return [];
    
    return value.split(VALUE_SEPARATOR);
} 

export function buildArrayField(value: string[]): string {
    return value.join(VALUE_SEPARATOR);
} 

export function buildSearchParam(filters: FilterQuery): string {
    const search: string[] = [];

    for (const key in filters) {
        search.push(`${key}${FIELD_VALUE_SEPARATOR}${filters[key]}`);
    }

    return search.join(FIELD_SEPARATOR);
}