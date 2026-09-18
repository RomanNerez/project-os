import { computed, type ComputedRef } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { buildQuery, readQuery, type QueryValue } from '@/shared/lib';
import { projectRoutes } from '@/entities/project';

export const PROJECTS_PER_PAGE_OPTIONS = [10, 20, 50];

const DEFAULT_PAGE = 1;
const DEFAULT_PER_PAGE = PROJECTS_PER_PAGE_OPTIONS[0];

interface UseProjectPagination {
    setPage: (page: number) => void;
    setPerPage: (perPage: number) => void;
}

export function useProjectPagination(): UseProjectPagination {
    const page = usePage();

    // Everything else in the address - filters above all - is carried over as is.
    function visit(params: Record<string, QueryValue>): void {
        router.get(projectRoutes.index(), buildQuery({ ...readQuery(page.url), ...params }), {
            preserveState: true,
            preserveScroll: true,
            only: ['projects'],
        });
    }

    function setPage(value: number): void {
        visit({ page: value === DEFAULT_PAGE ? null : value });
    }

    // A page number picked for one page size points at different projects under
    // another, so the size change starts over from the first page.
    function setPerPage(value: number): void {
        visit({ page: null, limit: value === DEFAULT_PER_PAGE ? null : value });
    }

    return { setPage, setPerPage };
}

/**
 * Whether the page size was chosen explicitly, as opposed to being the default.
 */
export function useExplicitPerPage(): ComputedRef<boolean> {
    const page = usePage();

    return computed(() => 'limit' in readQuery(page.url));
}
