<script setup lang="ts">
import { toRefs } from 'vue';
import { ConfirmModal } from '@/shared/ui';
import { useTaskForm, type Task } from '@/entities/task';

const props = defineProps<{
    task: Task | null;
}>();

const emit = defineEmits<{
    done: [];
    cancel: [];
}>();

const { task } = toRefs(props);

const visible = defineModel<boolean>('visible', { required: true });

const { form, remove } = useTaskForm(
    task,
    () => {
        emit('done');
        visible.value = false;
    },
);

function cancel() {
    emit('cancel');
    visible.value = false;
}
</script>

<template>
    <ConfirmModal
        v-model:visible="visible"
        header="Видалити задачу"
        icon="pi pi-exclamation-triangle"
        severity="danger"
        confirm-label="Видалити"
        :processing="form.processing"
        @confirm="remove"
        @cancel="cancel"
    >
        Ви впевнені, що хочете видалити задачу
        <span class="font-semibold">«{{ task?.title }}»</span>?
        Цю дію неможливо скасувати.
    </ConfirmModal>
</template>
