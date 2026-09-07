<script setup lang="ts">
import { ConfirmModal } from '@/shared/ui';
import { useProjectMemberDelete, type ProjectID, type ProjectMemberID } from '@/entities/project';

const props = defineProps<{
    projectId: ProjectID;
    memberId: ProjectMemberID;
    name: string;
}>();

const visible = defineModel<boolean>('visible', { required: true });
defineEmits<{
    onDone: [];
    onCancel: [];
}>();

const { form, remove } = useProjectMemberDelete();
</script>

<template>
    <ConfirmModal
        v-model:visible="visible"
        header="Видалити учасника з проєкту"
        icon="pi pi-exclamation-triangle"
        severity="danger"
        confirm-label="Видалити"
        :processing="form.processing"
        @confirm="remove(projectId, memberId, {onSuccess: () => $emit('onDone')})"
        @cancel="$emit('onCancel')"
    >
        Ви впевнені, що хочете видалити цього учасника
        <span class="font-semibold">«{{ name }}»</span>?
        Цю дію неможливо скасувати.
    </ConfirmModal>
</template>
