<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { AdminLayout } from '@/widgets/admin-layout';
import { TimeTracker } from '@/widgets/time-tracker';
import { TimeEntryList } from '@/widgets/time-entry-list';
import type { TimeEntry, TimeEntryProject } from '@/entities/time-entry';

interface Props {
    entries: {
        data: TimeEntry[];
    };
    running: {
        data: TimeEntry;
    } | null;
    projects: TimeEntryProject[];
    serverTime: string;
}

const props = defineProps<Props>();

const running = computed(() => props.running?.data ?? null);
</script>

<template>
    <Head title="Трекер часу" />

    <AdminLayout
        title="Трекер часу"
        description="Запускайте таймер і дивіться, куди йде ваш час"
    >
        <div class="flex h-full flex-col gap-6">
            <TimeTracker
                :running="running"
                :projects="props.projects"
                :server-time="props.serverTime"
            />

            <TimeEntryList :entries="props.entries.data" />
        </div>
    </AdminLayout>
</template>
