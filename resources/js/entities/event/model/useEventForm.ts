import { watch, type Ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { emptyEventDraft, eventToDraft, toPeriodPayload } from '../lib/period';
import { eventRoutes } from '../api/eventRoutes';
import type { CalendarEvent, EventDraft, EventPeriod } from './types';

/**
 * Creates an event when `event` is null (starting at `period`, in `projectId` if given), otherwise edits it.
 */
export function useEventForm(
    event: Ref<CalendarEvent | null>,
    period: Ref<EventPeriod | null>,
    projectId: Ref<number | null>,
    onDone: () => void,
) {
    const form = useForm<EventDraft>(emptyEventDraft());

    function reset(): void {
        form.defaults(event.value ? eventToDraft(event.value) : emptyEventDraft(period.value, projectId.value));
        form.reset();
        form.clearErrors();
    }

    watch([event, period], reset, { immediate: true });

    function submit(): void {
        if (!form.start_at) {
            form.setError('start_at', 'Вкажіть початок');
            return;
        }

        const current = event.value;
        const options = { onSuccess: onDone, preserveScroll: true, preserveState: true };

        form.transform((data) => ({
            ...data,
            ...toPeriodPayload(data.start_at, data.end_at, data.all_day, true),
        }));

        if (current) {
            form.put(eventRoutes.update(current.id), options);
        } else {
            form.post(eventRoutes.store(), options);
        }
    }

    return { form, submit, reset };
}
