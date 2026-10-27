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

/**
 * Start and stop timestamps are assigned by the server, so both actions send no
 * time at all — the client only says *what* is being tracked. Only the fields of the
 * current `mode` are sent: a task brings its own project and title.
 */
/** `projectId` preselects the project of a free-form entry. */
export function useTimeTracker(projectId: number | null = null) {
    const form = useForm<TimeEntryDraft>({ ...emptyTimeEntryDraft(), project_id: projectId });
    const mode = ref<TimeTrackerMode>(TIME_TRACKER_MODE.TASK);
    const options = { preserveScroll: true };

    function start(): void {
        form.transform((data) => mode.value === TIME_TRACKER_MODE.TASK
            ? { ...data, description: null, project_id: null }
            : { ...data, task_id: null },
        ).post(timeEntryRoutes.start(), {
            ...options,
            onSuccess: () => form.reset('description', 'task_id'),
        });
    }

    function stop(): void {
        router.post(timeEntryRoutes.stop(), {}, options);
    }

    return { form, mode, start, stop };
}

export function deleteTimeEntry(id: TimeEntryID): void {
    router.delete(timeEntryRoutes.destroy(id), { preserveScroll: true });
}
