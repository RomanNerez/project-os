import { useForm } from '@inertiajs/vue3';
import { timeEntryRoutes } from '../api/timeEntryRoutes';
import type { TimeEntry } from './types';

interface TimeEntryUpdateDraft {
    description: string;
    started_at: Date | null;
    stopped_at: Date | null;
}

export function useTimeEntryUpdate() {
    const form = useForm<TimeEntryUpdateDraft>({
        description: '',
        started_at: null,
        stopped_at: null,
    });

    function reset(entry: TimeEntry): void {
        form.defaults({
            description: entry.description,
            started_at: new Date(entry.started_at),
            stopped_at: entry.stopped_at ? new Date(entry.stopped_at) : null,
        });
        form.reset();
        form.clearErrors();
    }

    /** A task entry keeps the task's title, so only a free-form entry sends its description. */
    function submit(projectId: number, entry: TimeEntry, onSuccess: () => void): void {
        form.transform((data) => ({
            ...(entry.task ? {} : { description: data.description }),
            started_at: data.started_at?.toISOString() ?? null,
            stopped_at: data.stopped_at?.toISOString() ?? null,
        })).patch(timeEntryRoutes.update(projectId, entry.id), {
            preserveScroll: true,
            onSuccess,
        });
    }

    return { form, reset, submit };
}
