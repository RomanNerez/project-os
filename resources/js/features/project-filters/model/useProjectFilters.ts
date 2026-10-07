import { computed, ref, type ComputedRef, type Ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { buildQuery, debounce, excludeQueryParams, readQuery } from '@/shared/lib';
import {
    emptyProjectFilters,
    hasActiveProjectFilters,
    projectRoutes,
    type ProjectFilters,
    type ProjectStatus,
} from '@/entities/project';
import { buildFilterQuery, parseFilterQuery } from './searchQuery';

interface UseProjectFilters {
    filters: Ref<ProjectFilters>;
    isActive: ComputedRef<boolean>;
    setSearch: (value: string) => void;
    setStatuses: (value: ProjectStatus[]) => void;
    reset: () => void;
}

export function useProjectFilters(): UseProjectFilters {
    const page = usePage();
    const filters = ref<ProjectFilters>(parseFilterQuery(page.url));

    function apply(): void {
        const query = buildQuery({
            ...buildFilterQuery(filters.value),
            ...excludeQueryParams(readQuery(page.url), ['search', 'searchJoin', 'page']),
        });

        router.get(projectRoutes.index(), query, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['projects'],
            reset: ['projects'],
        });
    }

    const applyDebounced = debounce(apply, 300);

    function setSearch(value: string): void {
        filters.value.title = value;
        applyDebounced();
    }

    function setStatuses(value: ProjectStatus[]): void {
        filters.value.status = value;
        apply();
    }

    function reset(): void {
        filters.value = emptyProjectFilters();
        apply();
    }

    return {
        filters,
        isActive: computed(() => hasActiveProjectFilters(filters.value)),
        setSearch,
        setStatuses,
        reset,
    };
}

export function useActiveProjectFilters(): ComputedRef<boolean> {
    const page = usePage();

    return computed(() => hasActiveProjectFilters(parseFilterQuery(page.url)));
}
