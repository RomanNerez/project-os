import {
    emptyProjectFilters,
    type ProjectFilters,
    type ProjectStatus
} from '@/entities/project';
import {
    buildArrayField,
    buildSearchParam,
    parseArrayField,
    parseSearchParam,
    sanitizeSearchText,
    SEARCH_JOIN,
    type FilterQuery
} from '@/shared/lib';

export function buildFilterQuery(filters: ProjectFilters): FilterQuery {
    const params: FilterQuery = {};
    const title = sanitizeSearchText(filters.title);

    if (title) params.title = title;
    if (filters.status.length) params.status = buildArrayField(filters.status);

    if (!Object.keys(params).length) return {};

    return { search: buildSearchParam(params), searchJoin: SEARCH_JOIN.AND };
}

export function parseFilterQuery(url: string): ProjectFilters {
    const query = parseSearchParam(url);
    const filters = emptyProjectFilters();

    filters.title = query.title ?? '';
    filters.status = parseArrayField(query.status ?? '') as ProjectStatus[];

    return filters;
}
