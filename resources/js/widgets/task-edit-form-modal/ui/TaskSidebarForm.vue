<script setup lang="ts">
import { STATUS_OPTIONS, type TaskAssignee, type TaskDraft, type TaskProject } from '@/entities/task';
import { FormSelect } from '@/shared/ui';
import type { InertiaForm } from '@inertiajs/vue3';

interface Props {
    form: InertiaForm<TaskDraft>;
    projects: TaskProject[];
    assignees: TaskAssignee[];
}

defineProps<Props>()

</script>

<template>
    <FormSelect
        v-model="form.project_id"
        name="task-project"
        label="Проєкт"
        :options="projects"
        option-label="title"
        option-value="id"
        placeholder="Оберіть проєкт"
        :message="form.errors.project_id"
    />

    <FormSelect
        v-model="form.assignee_id"
        name="task-assignee"
        label="Виконавець"
        :options="assignees"
        option-label="name"
        option-value="id"
        placeholder="Не призначено"
        show-clear
        :message="form.errors.assignee_id"
    />

    <FormSelect
        v-model="form.status"
        name="task-status"
        label="Статус"
        :options="STATUS_OPTIONS"
        option-label="label"
        option-value="value"
        :message="form.errors.status"
    />
</template>