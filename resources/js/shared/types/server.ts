export type IncludedData<T> = {
    data: T;
}

export interface Pagination {
    count: number;
    current_page: number;
    links: object;
    per_page: number;
    total: number;
    total_pages: number;
}

export interface BaseMeta {
    include?: string[];
}

export interface PaginatedMeta extends BaseMeta {
    pagination: Pagination;
}

export interface ServerData<T> {
    data: T;
    meta?: BaseMeta;
}

export interface PaginatedServerData<T> extends ServerData<T> {
    meta: PaginatedMeta;
}