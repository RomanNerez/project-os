<script setup lang="ts">
import { type Task, type TaskAssignee } from '@/entities/task';
import { TaskAssigneeField, TaskEstimateField, TaskPriorityField, TaskStatusField } from '@/features/task-inline-edit';

interface Props {
    task: Task;
    assignees: TaskAssignee[];
    disabled?: boolean;
}

defineProps<Props>()

</script>

<template>
    <TaskAssigneeField :task="task" :options="assignees" :disabled="disabled" />

    <TaskStatusField :task="task" :disabled="disabled" />

    <TaskPriorityField :task="task" :disabled="disabled" />

    <TaskEstimateField :task="task" :disabled="disabled" />
</template>
