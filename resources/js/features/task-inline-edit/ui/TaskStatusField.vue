<script setup lang="ts">
import { toRefs } from 'vue';
import { STATUS_OPTIONS, useTaskFieldForm, type Task, type TaskStatus } from '@/entities/task';
import { FormSelect } from '@/shared/ui';

const props = defineProps<{
    task: Task;
    disabled?: boolean;
}>();

const { task } = toRefs(props);

const { form, error, save } = useTaskFieldForm(task, 'status', { revertOnError: true });

function onChange(status: TaskStatus | null): void {
    form.value = status;
    save();
}
</script>

<template>
    <FormSelect
        :model-value="form.value"
        name="task-status"
        label="Статус"
        :options="STATUS_OPTIONS"
        option-label="label"
        option-value="value"
        :disabled="disabled"
        :loading="form.processing"
        :message="error"
        @update:model-value="onChange"
    />
</template>
