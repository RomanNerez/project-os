<script setup lang="ts">
import { toRefs } from 'vue';
import { PRIORITY_OPTIONS, useTaskFieldForm, type Task, type TaskPriority } from '@/entities/task';
import { FormSelect } from '@/shared/ui';

const props = defineProps<{
    task: Task;
    disabled?: boolean;
}>();

const { task } = toRefs(props);

const { form, error, save } = useTaskFieldForm(task, 'priority', { revertOnError: true });

function onChange(priority: TaskPriority | null): void {
    form.value = priority;
    save();
}
</script>

<template>
    <FormSelect
        :model-value="form.value"
        name="task-priority"
        label="Пріоритет"
        :options="PRIORITY_OPTIONS"
        option-label="label"
        option-value="value"
        :disabled="disabled"
        :loading="form.processing"
        :message="error"
        @update:model-value="onChange"
    />
</template>
