import { usePage } from "@inertiajs/vue3";
import { computed, type ComputedRef } from "vue";
import { hasActiveFilters, parseFilterQuery } from "./searchQuery";

export function useActiveTaskFilters(): ComputedRef<boolean> {
    const page = usePage();

    return computed(() => hasActiveFilters(parseFilterQuery(page.url)));
}