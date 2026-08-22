<script setup lang="ts">
import { computed } from 'vue';
import { formatDuration, formatTimeOfDay } from '@/shared/lib';
import type { TimeEntry } from '../model/types';

const props = defineProps<{
    entry: TimeEntry;
}>();

defineEmits<{
    delete: [];
}>();

const range = computed(() => {
    const start = formatTimeOfDay(props.entry.started_at);

    return props.entry.stopped_at
        ? `${start} — ${formatTimeOfDay(props.entry.stopped_at)}`
        : start;
});
</script>

<template>
    <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium">{{ entry.description }}</p>
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ range }}</p>
        </div>

        <Tag
            v-if="entry.project"
            :value="entry.project.title"
            severity="secondary"
            class="hidden shrink-0 md:inline-flex"
        />

        <span class="shrink-0 font-mono text-sm tabular-nums">
            {{ entry.duration === null ? '—' : formatDuration(entry.duration) }}
        </span>

        <Button
            icon="pi pi-trash"
            severity="danger"
            text
            size="small"
            aria-label="Видалити запис"
            @click="$emit('delete')"
        />
    </div>
</template>
