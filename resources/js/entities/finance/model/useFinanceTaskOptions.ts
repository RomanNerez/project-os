import { computed, ref, type ComputedRef, type Ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import { debounce, sanitizeSearchText } from '@/shared/lib';
import { financeRoutes } from '../api/financeRoutes';
import type { FinanceTaskHours } from './types';

interface FinanceTaskOptionsQuery {
    title: string;
    except_finance_id: number | null;
    limit: number;
}

interface FinanceTaskOptionsParams {
    limit?: 10 | 20 | 50;
}

interface UseFinanceTaskOptions {
    tasks: Ref<FinanceTaskHours[]>;
    loading: ComputedRef<boolean>;
    load: (projectId: number, title?: string, exceptFinanceId?: number | null) => Promise<FinanceTaskHours[]>;
    search: (projectId: number, title: string, exceptFinanceId?: number | null) => void;
}

/** Tasks of the project with unpaid tracked time; `exceptFinanceId` frees the time paid by the edited record. */
export function useFinanceTaskOptions({ limit = 50 }: FinanceTaskOptionsParams = {}): UseFinanceTaskOptions {
    const tasks = ref<FinanceTaskHours[]>([]);

    const http = useHttp<FinanceTaskOptionsQuery, { data: FinanceTaskHours[] }>({
        title: '',
        except_finance_id: null,
        limit,
    });

    const loading = computed(() => http.processing);

    async function load(projectId: number, title: string = '', exceptFinanceId: number | null = null): Promise<FinanceTaskHours[]> {
        http.cancel();

        http.title = sanitizeSearchText(title);
        http.except_finance_id = exceptFinanceId;

        try {
            const response = await http.get(financeRoutes.tasks(projectId));
            tasks.value = response?.data ?? [];
        } catch {
            tasks.value = [];
        }

        return tasks.value;
    }

    const search = debounce(
        (projectId: number, title: string, exceptFinanceId: number | null = null) => void load(projectId, title, exceptFinanceId),
        300,
    );

    return { tasks, loading, load, search };
}
