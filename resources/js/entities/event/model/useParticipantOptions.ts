import { computed, ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import { eventRoutes } from '../api/eventRoutes';
import type { ParticipantOption } from './types';

/**
 * Users who can be invited: the project's participants, or colleagues from shared projects
 * for a personal event (`projectId` null).
 */
export function useParticipantOptions() {
    const options = ref<ParticipantOption[]>([]);

    const http = useHttp<{ project_id: number | '' }, { data: ParticipantOption[] }>({ project_id: '' });

    async function load(projectId: number | null): Promise<boolean> {
        http.cancel();
        http.project_id = projectId ?? '';

        try {
            const response = await http.get(eventRoutes.participants());
            options.value = response?.data ?? [];

            return true;
        } catch {
            // Cancelled by a newer load or failed: keep the current options.
            return false;
        }
    }

    return { options, loading: computed(() => http.processing), load };
}
