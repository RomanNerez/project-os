import { computed, ref, type ComputedRef, type Ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import { buildSearchParam, debounce, sanitizeSearchText, SEARCH_JOIN, type FilterQuery } from '@/shared/lib';
import { taskRoutes } from '../api/taskRoutes';
import type { TaskOption } from './types';

interface TaskOptionsQuery {
    search: string;
    searchJoin: typeof SEARCH_JOIN.AND;
    limit: number;
}

interface TaskOptionsParams {
    limit?: 10 | 20 | 50;
}

interface UseTaskOptions {
    tasks: Ref<TaskOption[]>;
    loading: ComputedRef<boolean>;
    loadByProject: (projectId: number, title?: string) => Promise<void>;
    search: (projectId: number, title: string) => void;
}

export function useTaskOptions({ limit = 20 }: TaskOptionsParams = {}): UseTaskOptions {
    const tasks = ref<TaskOption[]>([]);

    const http = useHttp<TaskOptionsQuery, { data: TaskOption[] }>({
        search: '',
        searchJoin: SEARCH_JOIN.AND,
        limit,
    });

    const loading = computed(() => http.processing);

    async function loadByProject(projectId: number, title: string = ''): Promise<void> {
        const value = sanitizeSearchText(title);

        http.cancel();

        const filters: FilterQuery = {};

        if (value) filters.title = value;

        http.search = buildSearchParam(filters);

        try {
            const response = await http.get(taskRoutes.options(projectId));
            tasks.value = response?.data ?? [];
        } catch {

        }
    }

    const search = debounce(
        (projectId: number, title: string) => void loadByProject(projectId, title),
        300
    );

    return { tasks, loading, loadByProject, search };
}
