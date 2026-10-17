import { computed, ref, type ComputedRef, type Ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import { buildSearchParam, debounce, sanitizeSearchText, SEARCH_JOIN } from '@/shared/lib';
import { taskRoutes } from '../api/taskRoutes';
import type { TaskOption } from './types';

interface TaskOptionsQuery {
    search: string;
    searchJoin: typeof SEARCH_JOIN.AND;
    limit: number;
}

interface UseTaskOptions {
    tasks: Ref<TaskOption[]>;
    loading: ComputedRef<boolean>;
    load: (title?: string) => Promise<void>;
    search: (title: string) => void;
}

/**
 * Tasks visible to the current user (with their project), loaded from `tasks.index` as JSON.
 */
export function useTaskOptions(): UseTaskOptions {
    const tasks = ref<TaskOption[]>([]);

    const http = useHttp<TaskOptionsQuery, { data: TaskOption[] }>({
        search: '',
        searchJoin: SEARCH_JOIN.AND,
        limit: 20,
    });

    async function load(title = ''): Promise<void> {
        const value = sanitizeSearchText(title);

        http.cancel();
        http.search = value ? buildSearchParam({ title: value }) : '';

        try {
            const response = await http.get(taskRoutes.index());
            tasks.value = response?.data ?? [];
        } catch {
            // Cancelled by a newer search or failed: keep the current options.
        }
    }

    return {
        tasks,
        loading: computed(() => http.processing),
        load,
        search: debounce((title: string) => void load(title), 300),
    };
}
