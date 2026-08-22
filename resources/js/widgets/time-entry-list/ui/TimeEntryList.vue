<script setup lang="ts">
import { computed } from 'vue';
import { formatDayLabel, formatDuration, toDayKey } from '@/shared/lib';
import { TimeEntryRow, deleteTimeEntry, type TimeEntry } from '@/entities/time-entry';

const props = defineProps<{
    entries: TimeEntry[];
}>();

interface DayGroup {
    key: string;
    label: string;
    total: number;
    entries: TimeEntry[];
}

const groups = computed<DayGroup[]>(() => {
    const byDay = new Map<string, TimeEntry[]>();

    props.entries.forEach((entry) => {
        const key = toDayKey(entry.started_at);
        const group = byDay.get(key);

        group ? group.push(entry) : byDay.set(key, [entry]);
    });

    return [...byDay.entries()].map(([key, entries]) => ({
        key,
        label: formatDayLabel(key),
        total: entries.reduce((sum, entry) => sum + (entry.duration ?? 0), 0),
        entries,
    }));
});
</script>

<template>
    <div v-if="groups.length" class="flex flex-col gap-6">
        <section v-for="group in groups" :key="group.key" class="flex flex-col gap-2">
            <header class="flex items-baseline justify-between px-1">
                <h2 class="text-sm font-semibold capitalize">{{ group.label }}</h2>
                <span class="font-mono text-sm tabular-nums text-muted-color">
                    {{ formatDuration(group.total) }}
                </span>
            </header>

            <TimeEntryRow
                v-for="entry in group.entries"
                :key="entry.id"
                :entry="entry"
                @delete="deleteTimeEntry(entry.id)"
            />
        </section>
    </div>

    <div
        v-else
        class="flex flex-col items-center gap-3 rounded-lg border border-dashed border-gray-300 py-16 dark:border-gray-600"
    >
        <i class="pi pi-stopwatch text-3xl text-gray-500"></i>
        <p class="text-gray-500">Записів поки немає</p>
        <p class="text-sm text-gray-500">Введіть назву задачі вгорі та натисніть «Старт»</p>
    </div>
</template>
