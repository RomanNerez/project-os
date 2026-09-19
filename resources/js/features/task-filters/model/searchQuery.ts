import { buildArrayField, buildSearchParam, parseArrayField, parseSearchParam, sanitizeSearchText, SEARCH_JOIN, type FilterQuery } from "@/shared/lib";
import type { TaskFilters } from "./types";
import { emptyTaskFilters } from "../config/defaults";
import type { TaskStatus } from "@/entities/task";

export function buildFilterQuery(filters: TaskFilters): FilterQuery {
    const params: FilterQuery = {};
    const title = sanitizeSearchText(filters.title);

    if (title) params.title = title;
    if (filters.status.length) params.status = buildArrayField(filters.status);

    if (!Object.keys(params).length) return {};

    return { search: buildSearchParam(params), searchJoin: SEARCH_JOIN.AND };
}

export function parseFilterQuery(url: string): TaskFilters {
    const query = parseSearchParam(url);
    const filters = emptyTaskFilters();

    filters.title = query.title ?? '';
    filters.status = parseArrayField(query.status ?? '') as TaskStatus[];

    return filters;
}

export function hasActiveFilters(filters: TaskFilters) {
    return filters.title !== '' || filters.status.length !== 0;
}