import { watch, type Ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { emptyRoleDraft, toRoleDraft, type RoleDraft, type WorkspaceRole } from './types';
import { roleRoutes } from '../api/roleRoutes';

export function useRoleForm(role: Ref<WorkspaceRole | null>, onDone: () => void) {
    const form = useForm<RoleDraft>(emptyRoleDraft());
    const options = { onSuccess: onDone, preserveScroll: true };

    function reset(): void {
        form.defaults(role.value ? toRoleDraft(role.value) : emptyRoleDraft());
        form.reset();
        form.clearErrors();
    }

    watch(role, reset, { immediate: true });

    function submit(): void {
        role.value
            ? form.put(roleRoutes.update(role.value.id), options)
            : form.post(roleRoutes.store(), options);
    }

    function remove(): void {
        if (!role.value) return;

        form.delete(roleRoutes.destroy(role.value.id), options);
    }

    return { form, submit, remove, reset };
}
