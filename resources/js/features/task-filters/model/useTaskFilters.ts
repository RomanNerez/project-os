import { router, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import type { TaskFilters } from "./types";
import { buildQuery, debounce, excludeQueryParams, readQuery } from "@/shared/lib";
import type { TaskStatus } from "@/entities/task";
import { emptyTaskFilters } from "../config/defaults";
import { buildFilterQuery, hasActiveFilters, parseFilterQuery } from "./searchQuery";

export function useTaskFilters() {
    const filters = ref<TaskFilters>(emptyTaskFilters());
    const page = usePage();

    const isActive = computed(() => hasActiveFilters(filters.value));

    onMounted(() => {
        filters.value = parseFilterQuery(page.url);
    });

    function apply(): void {
        const query = buildQuery({
            ...buildFilterQuery(filters.value),
            ...excludeQueryParams(readQuery(page.url), ['search', 'searchJoin']),
        });

        router.get(route('tasks.index'), query, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['tasks'],
        });
    }

    const applyDebounced = debounce(apply, 300);

    const setSearch = (value: string) => {
        filters.value.title = value;
        applyDebounced();
    }

    const setStatuses = (value: TaskStatus[]) => {
        filters.value.status = value;
        apply();
    }

    const reset = () => {
        filters.value = emptyTaskFilters();
        apply();
    }

    return { filters, isActive, setSearch, setStatuses, reset };
}