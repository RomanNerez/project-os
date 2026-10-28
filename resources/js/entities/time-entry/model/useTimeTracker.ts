import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { timeEntryRoutes } from '../api/timeEntryRoutes';
import {
    emptyTimeEntryDraft,
    TIME_TRACKER_MODE,
    type TimeEntryDraft,
    type TimeEntryID,
    type TimeTrackerMode,
} from './types';

export function useTimeTracker() {
    const form = useForm<TimeEntryDraft>(emptyTimeEntryDraft());
    const mode = ref<TimeTrackerMode>(TIME_TRACKER_MODE.TASK);
    const options = { preserveScroll: true };

    function start(projectId: number): void {
        form.transform((data) => mode.value === TIME_TRACKER_MODE.TASK
            ? { ...data, description: null }
            : { ...data, task_id: null },
        ).post(timeEntryRoutes.start(projectId), {
            ...options,
            onSuccess: () => form.reset('description', 'task_id'),
        });
    }

    function stop(projectId: number): void {
        router.post(timeEntryRoutes.stop(projectId), {}, options);
    }

    return { form, mode, start, stop };
}

export function deleteTimeEntry(projectId: number, timeEntryId: TimeEntryID): void {
    router.delete(timeEntryRoutes.destroy(projectId, timeEntryId), { preserveScroll: true });
}
