import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import type { VisitOptions } from '@inertiajs/core';
import { userRoutes } from '../api/userRoutes';

export function useResendInvitation() {
    const resendingId = ref<number | null>(null);

    function resend(id: number, options?: Partial<VisitOptions>): void {
        router.post(userRoutes.resendInvite(id), {}, {
            preserveScroll: true,
            preserveState: true,
            only: ['invitations', 'errors'],
            ...options,
            onStart: (visit) => {
                resendingId.value = id;
                options?.onStart?.(visit);
            },
            onFinish: (visit) => {
                resendingId.value = null;
                options?.onFinish?.(visit);
            },
        });
    }

    return { resend, resendingId };
}
