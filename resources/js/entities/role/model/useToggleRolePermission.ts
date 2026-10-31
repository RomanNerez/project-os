import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { toRoleDraft, type WorkspaceRole } from './types';
import { roleRoutes } from '../api/roleRoutes';

/** Saves a single permission change of a role right away (the permissions matrix). */
export function useToggleRolePermission() {
    /** `${roleId}:${permission}` of the cell being saved. */
    const pendingKey = ref<string | null>(null);

    function toggle(role: WorkspaceRole, permission: string, enabled: boolean): void {
        const permissions = enabled
            ? [...new Set([...role.permissions, permission])]
            : role.permissions.filter((name) => name !== permission);

        router.put(roleRoutes.update(role.id), { ...toRoleDraft(role), permissions }, {
            only: ['roles'],
            preserveScroll: true,
            preserveState: true,
            onStart: () => { pendingKey.value = `${role.id}:${permission}`; },
            onFinish: () => { pendingKey.value = null; },
        });
    }

    return { toggle, pendingKey };
}
