<script setup lang="ts">
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ProjectLayout, type BreadcrumbItem } from '@/widgets/project-layout';
import { projectRoutes, type Project } from '@/entities/project';
import { TimeTracker } from '@/widgets/time-tracker';
import { TimeEntryList } from '@/widgets/time-entry-list';
import type { TimeEntry } from '@/entities/time-entry';
import type { ServerData } from '@/shared/types';

interface Props {
    project: ServerData<Project>;
    entries:  ServerData<TimeEntry[]>;
    running: ServerData<TimeEntry> | null;
    serverTime: string;
}

const props = defineProps<Props>();

const project = computed(() => props.project.data);

const running = computed(() => props.running?.data ?? null);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { label: 'Проєкти', href: projectRoutes.index() },
    { label: project.value.title, href: projectRoutes.show(project.value.id) },
    { label: 'Трекер часу' },
]);
</script>

<template>
    <Head title="Трекер часу" />

    <ProjectLayout
        title="Трекер часу"
        description="Запускайте таймер і дивіться, куди йде ваш час"
        :breadcrumbs="breadcrumbs"
    >
        <div class="flex h-full flex-col gap-6">
            <TimeTracker
                :running="running"
                :server-time="props.serverTime"
                :project-id="project.id"
            />

            <TimeEntryList :project-id="project.id" :entries="props.entries.data" />
        </div>
    </ProjectLayout>
</template>
