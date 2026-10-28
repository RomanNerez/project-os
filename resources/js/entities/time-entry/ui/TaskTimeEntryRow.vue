<script setup lang="ts">
import { computed } from 'vue';
import { UserAvatar } from '@/shared/ui';
import { formatDateTimeRange, formatDurationHuman } from '@/shared/lib';
import type { TimeEntry } from '../model/types';

const props = defineProps<{
    userName: string;
    userAvatar: string | null;
    entry: TimeEntry;
}>();

const range = computed(() => props.entry.stopped_at
    ? formatDateTimeRange(props.entry.started_at, props.entry.stopped_at)
    : '');

const duration = computed(() => formatDurationHuman(props.entry.duration ?? 0));
</script>

<template>
    <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <UserAvatar :name="userName" :image="userAvatar" class="flex-shrink-0" />

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium">{{ userName }}</p>
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ range }}</p>
        </div>

        <span class="shrink-0 text-sm font-semibold tabular-nums">{{ duration }}</span>
    </div>
</template>
