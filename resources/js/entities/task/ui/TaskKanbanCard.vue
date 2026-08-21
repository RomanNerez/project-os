<script setup lang="ts">
import { computed } from 'vue';
import type { Task } from '../model/types';

const props = defineProps<{
    task: Task;
}>();

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
    <div class="cursor-grab rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition-shadow hover:shadow active:cursor-grabbing dark:border-gray-700 dark:bg-gray-800">
        <p class="text-sm font-medium">{{ task.title }}</p>
        <p v-if="task.description" class="mt-1 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">
            {{ task.description }}
        </p>

        <div class="mt-3 flex items-center justify-between gap-2">
            <div class="flex min-w-0 items-center gap-2">
                <Avatar
                    v-if="task.assignee"
                    :label="assigneeInitials"
                    shape="circle"
                    class="size-6 shrink-0 text-xs"
                />
                <Avatar v-else icon="pi pi-user" shape="circle" class="size-6 shrink-0 text-xs" />
                <span class="truncate text-xs text-gray-500 dark:text-gray-400">
                    {{ task.assignee?.name ?? 'Не призначено' }}
                </span>
            </div>
            <span class="shrink-0 text-xs text-gray-400">#{{ task.id }}</span>
        </div>
    </div>
</template>
