import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { toPeriodPayload } from '../lib/period';
import { eventRoutes } from '../api/eventRoutes';
import type { EventID, EventPeriod, ParticipantStatus } from './types';

interface Callbacks {
    onSuccess?: () => void;
    onError?: () => void;
}

/** Calendar mutations that need no form: drag & drop, delete, accept/decline. */
export function useEventActions() {
    const processing = ref(false);

    const visitOptions = ({ onSuccess, onError }: Callbacks) => ({
        preserveScroll: true,
        preserveState: true,
        onStart: () => { processing.value = true; },
        onFinish: () => { processing.value = false; },
        onSuccess: () => onSuccess?.(),
        onError: () => onError?.(),
    });

    function move(id: EventID, { start, end, allDay }: EventPeriod, callbacks: Callbacks = {}): void {
        router.patch(eventRoutes.move(id), toPeriodPayload(start, end, allDay, false), visitOptions(callbacks));
    }

    function remove(id: EventID, callbacks: Callbacks = {}): void {
        router.delete(eventRoutes.destroy(id), visitOptions(callbacks));
    }

    function respond(id: EventID, status: Exclude<ParticipantStatus, 'pending'>, callbacks: Callbacks = {}): void {
        router.patch(eventRoutes.respond(id), { status }, visitOptions(callbacks));
    }

    return { processing, move, remove, respond };
}
