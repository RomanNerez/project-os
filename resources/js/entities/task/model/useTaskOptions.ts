import { computed, ref, type ComputedRef, type Ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import { buildArrayField, buildSearchParam, debounce, sanitizeSearchText, SEARCH_JOIN, type FilterQuery } from '@/shared/lib';
import { taskRoutes } from '../api/taskRoutes';
import type { TaskOption } from './types';

interface TaskOptionsQuery {
    search: string;
    searchJoin: typeof SEARCH_JOIN.AND;
    limit: number;
}

interface TaskOptionsParams {
    /** Only tasks of these projects; empty means all visible projects. */
    projectIds?: () => number[];
    limit?: 10 | 20 | 50;
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
export function useTaskOptions({ projectIds = () => [], limit = 20 }: TaskOptionsParams = {}): UseTaskOptions {
    const tasks = ref<TaskOption[]>([]);

    const http = useHttp<TaskOptionsQuery, { data: TaskOption[] }>({
        search: '',
        searchJoin: SEARCH_JOIN.AND,
        limit,
    });

    async function load(title = ''): Promise<void> {
        const value = sanitizeSearchText(title);

        http.cancel();

        const filters: FilterQuery = {};
        const ids = projectIds();

        if (value) filters.title = value;
        if (ids.length) filters.project_id = buildArrayField(ids.map(String));

        http.search = buildSearchParam(filters);

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
