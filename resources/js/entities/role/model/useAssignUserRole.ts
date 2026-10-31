import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import type { RoleID } from './types';
import { roleRoutes } from '../api/roleRoutes';

export function useAssignUserRole() {
    const assigningUserId = ref<number | null>(null);

    function assign(userId: number, roleId: RoleID | null): void {
        router.put(roleRoutes.assignToUser(userId), { role_id: roleId }, {
            only: ['users', 'roles'],
            preserveScroll: true,
            preserveState: true,
            onStart: () => { assigningUserId.value = userId; },
            onFinish: () => { assigningUserId.value = null; },
        });
    }

    return { assign, assigningUserId };
}
