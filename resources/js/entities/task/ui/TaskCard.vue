<script setup lang="ts">
import { computed } from 'vue';
import { STATUS_META, type Task } from '../model/types';

const props = defineProps<{
    task: Task;
}>();

defineEmits<{
    edit: [];
    delete: [];
}>();

const status = computed(() => STATUS_META[props.task.status]);

const assigneeInitials = computed(() =>
    (props.task.assignee?.name ?? '')
        .split(' ')
        .filter(Boolean)
        .map((part) => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase(),
);
</script>

<template>
    <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium">{{ task.title }}</p>
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ task.description }}</p>
        </div>

        <Tag
            v-if="task.project"
            :value="task.project.title"
            severity="secondary"
            class="hidden shrink-0 md:inline-flex"
        />

        <div class="hidden w-44 shrink-0 items-center gap-2 lg:flex">
            <Avatar
                v-if="task.assignee"
                :label="assigneeInitials"
                shape="circle"
                class="size-7 shrink-0 text-xs"
            />
            <Avatar v-else icon="pi pi-user" shape="circle" class="size-7 shrink-0 text-xs" />
            <span class="truncate text-sm" :class="{ 'text-gray-500 dark:text-gray-400': !task.assignee }">
                {{ task.assignee?.name ?? 'Не призначено' }}
            </span>
        </div>

        <Tag :value="status.label" :severity="status.severity" class="shrink-0" />

        <div class="flex shrink-0 gap-1">
            <Button icon="pi pi-pencil" severity="secondary" text size="small" aria-label="Редагувати" @click="$emit('edit')" />
            <Button icon="pi pi-trash" severity="danger" text size="small" aria-label="Видалити" @click="$emit('delete')" />
        </div>
    </div>
</template>
