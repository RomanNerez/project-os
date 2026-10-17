<script setup lang="ts">
import { ConfirmModal } from '@/shared/ui';
import { useTaskDelete, type TaskID } from '@/entities/task';

const props = defineProps<{
    projectId: number;
    taskId: TaskID;
    title: string;
}>();

const emit = defineEmits(['onDone', 'onCancel']);

const visible = defineModel<boolean>('visible', { required: true });

const { isDeleting, destroy } = useTaskDelete();

function remove() {
    destroy(props.projectId, props.taskId, {
        onSuccess: () => emit('onDone')
    });
}
</script>

<template>
    <ConfirmModal
        v-model:visible="visible"
        header="Видалити задачу"
        icon="pi pi-exclamation-triangle"
        severity="danger"
        confirm-label="Видалити"
        :processing="isDeleting"
        @confirm="remove"
        @cancel="$emit('onCancel')"
    >
        Ви впевнені, що хочете видалити задачу
        <span class="font-semibold">«{{ title }}»</span>?
        Цю дію неможливо скасувати.
    </ConfirmModal>
</template>
