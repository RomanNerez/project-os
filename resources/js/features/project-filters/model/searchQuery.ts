import { emptyProjectFilters, isProjectStatus, type ProjectFilters } from '@/entities/project';

const FIELD_SEPARATOR = ';';
const VALUE_SEPARATOR = ',';

/**
 * `:` and `;` structure the `search` parameter, `%` and `_` are wildcards of the
 * partial match. None of them are escaped down the line, so they are dropped here.
 */
const RESERVED = /[:;%_]/g;

export type FilterQuery = Record<string, string>;

export function sanitizeSearchText(value: string): string {
    return value.replace(RESERVED, '').trim();
}

export function buildFilterQuery(filters: ProjectFilters): FilterQuery {
    const parts: string[] = [];
    const title = sanitizeSearchText(filters.search);

    if (title) parts.push(`title:${title}`);
    if (filters.statuses.length) parts.push(`status:${filters.statuses.join(VALUE_SEPARATOR)}`);

    if (!parts.length) return {};

    return { search: parts.join(FIELD_SEPARATOR), searchJoin: 'and' };
}

export function parseFilterQuery(url: string): ProjectFilters {
    const query = new URLSearchParams(url.split('?')[1] ?? '');
    const filters = emptyProjectFilters();

    for (const part of (query.get('search') ?? '').split(FIELD_SEPARATOR)) {
        const separator = part.indexOf(':');
        if (separator === -1) continue;

        const field = part.slice(0, separator);
        const value = part.slice(separator + 1);

        if (field === 'title') filters.search = value;
        if (field === 'status') filters.statuses = value.split(VALUE_SEPARATOR).filter(isProjectStatus);
    }

    return filters;
}
