import { computed, ref, watch, type ComputedRef, type Ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { buildQuery, debounce, readQuery } from '@/shared/lib';
import {
    emptyProjectFilters,
    hasActiveProjectFilters,
    projectRoutes,
    type ProjectFilters,
    type ProjectStatus,
} from '@/entities/project';
import { buildFilterQuery, parseFilterQuery } from './searchQuery';

const SEARCH_DELAY = 300;

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
            limit: readQuery(page.url).limit,
        });

        router.get(projectRoutes.index(), query, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['projects'],
        });
    }

    const applyDebounced = debounce(apply, SEARCH_DELAY);

    function setSearch(value: string): void {
        filters.value.search = value;
        applyDebounced();
    }

    function setStatuses(value: ProjectStatus[]): void {
        filters.value.statuses = value;
        apply();
    }

    function reset(): void {
        filters.value = emptyProjectFilters();
        apply();
    }

    const queryOf = (value: ProjectFilters): string => JSON.stringify(buildFilterQuery(value));

    // The address can change without remounting the panel - history navigation, for
    // one. Our own visits leave the URL matching the panel, so this only fires when
    // something else moved it.
    watch(() => page.url, (url) => {
        const fromUrl = parseFilterQuery(url);

        if (queryOf(fromUrl) !== queryOf(filters.value)) filters.value = fromUrl;
    });

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
