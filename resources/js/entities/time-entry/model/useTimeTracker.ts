import { router, useForm } from '@inertiajs/vue3';
import { timeEntryRoutes } from '../api/timeEntryRoutes';
import { emptyTimeEntryDraft, type TimeEntryDraft, type TimeEntryID } from './types';

/**
 * Start and stop timestamps are assigned by the server, so both actions send no
 * time at all — the client only says *what* is being tracked.
 */
export function useTimeTracker() {
    const form = useForm<TimeEntryDraft>(emptyTimeEntryDraft());
    const options = { preserveScroll: true };

    function start(): void {
        form.post(timeEntryRoutes.start(), {
            ...options,
            onSuccess: () => form.reset('description'),
        });
    }

    function stop(): void {
        router.post(timeEntryRoutes.stop(), {}, options);
    }

    return { form, start, stop };
}

export function deleteTimeEntry(id: TimeEntryID): void {
    router.delete(timeEntryRoutes.destroy(id), { preserveScroll: true });
}
