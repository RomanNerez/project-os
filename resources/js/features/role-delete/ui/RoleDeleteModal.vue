<script setup lang="ts">
import { toRefs } from 'vue';
import { ConfirmModal } from '@/shared/ui';
import { roleLabel, useRoleForm, type WorkspaceRole } from '@/entities/role';

const props = defineProps<{
    role: WorkspaceRole | null;
}>();

const { role } = toRefs(props);

const visible = defineModel<boolean>('visible', { required: true });
const emit = defineEmits<{
    done: [];
    cancel: [];
}>();

const { form, remove } = useRoleForm(role, () => {
    visible.value = false;
    emit('done');
});

function cancel(): void {
    visible.value = false;
    emit('cancel');
}
</script>

<template>
    <ConfirmModal
        v-model:visible="visible"
        header="Видалити роль"
        icon="pi pi-exclamation-triangle"
        severity="danger"
        confirm-label="Видалити"
        :processing="form.processing"
        @confirm="remove"
        @cancel="cancel"
    >
        Ви впевнені, що хочете видалити роль
        <span class="font-semibold">«{{ role ? roleLabel(role) : '' }}»</span>?
        <template v-if="role?.users_count">
            Її втратять користувачі ({{ role.users_count }}), разом із наданими нею дозволами.
        </template>
        Цю дію неможливо скасувати.
    </ConfirmModal>
</template>
