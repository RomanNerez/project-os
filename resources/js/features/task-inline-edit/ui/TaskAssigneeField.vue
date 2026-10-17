<script setup lang="ts">
import { toRefs } from 'vue';
import { useTaskFieldForm, type Task, type TaskAssignee } from '@/entities/task';
import { FormSelect } from '@/shared/ui';

const props = defineProps<{
    task: Task;
    options: TaskAssignee[];
    disabled?: boolean;
}>();

const { task } = toRefs(props);

const { form, error, save } = useTaskFieldForm(task, 'assignee_id', { revertOnError: true });

function onChange(assigneeId: number | null): void {
    form.value = assigneeId ?? null;
    save();
}
</script>

<template>
    <FormSelect
        :model-value="form.value"
        name="task-assignee"
        label="Виконавець"
        :options="options"
        option-label="name"
        option-value="id"
        placeholder="Не призначено"
        show-clear
        :disabled="disabled"
        :loading="form.processing"
        :message="error"
        @update:model-value="onChange"
    />
</template>
