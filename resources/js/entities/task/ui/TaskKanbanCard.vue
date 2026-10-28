<script setup lang="ts">
import { computed } from 'vue';
import { formatEstimate, isBlankHtml } from '@/shared/lib';
import { UserAvatar } from '@/shared/ui';
import { PRIORITY_META, type TaskWithAssignee } from '../model/types';

const props = withDefaults(defineProps<{
    task: TaskWithAssignee;
    commentsCount?: number;
    filesCount?: number;
}>(), {
    commentsCount: 0,
    filesCount: 0,
});

const priority = computed(() => PRIORITY_META[props.task.priority]);

const assignee = computed(() => props.task.assignee?.data ?? null);

const hasDescription = computed(() => !isBlankHtml(props.task.description));
</script>

<template>
    <div class="cursor-grab rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition-shadow hover:shadow active:cursor-grabbing dark:border-gray-700 dark:bg-gray-800">
        <p class="text-sm font-medium">{{ task.title }}</p>

        <div class="mt-2 flex items-center gap-2">
            <Tag :value="priority.label" :icon="priority.icon" :severity="priority.severity" class="!px-1.5 !py-0.5 !text-xs" />
            <span v-if="task.estimate" class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                <i class="pi pi-clock text-xs" />
                {{ formatEstimate(task.estimate) }}
            </span>
        </div>

        <div class="mt-3 flex items-center justify-between gap-2">
            <div v-if="assignee" class="flex min-w-0 items-center gap-2">
                <UserAvatar :name="assignee.name" :image="assignee.avatar_url" class="size-6 shrink-0 text-xs" />
                <span class="truncate text-xs text-gray-500 dark:text-gray-400">
                    {{ assignee.name }}
                </span>
            </div>

            <div class="ml-auto flex shrink-0 items-center gap-2 text-xs text-gray-400">
                <i v-if="hasDescription" v-tooltip.top="'Є опис'" class="pi pi-align-left text-xs" />
                <span v-if="filesCount" class="flex items-center gap-1">
                    <i class="pi pi-paperclip text-xs" />
                    {{ filesCount }}
                </span>
                <span v-if="commentsCount" class="flex items-center gap-1">
                    <i class="pi pi-comments text-xs" />
                    {{ commentsCount }}
                </span>
                <span>#{{ task.id }}</span>
            </div>
        </div>
    </div>
</template>
