<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { AdminLayout, type BreadcrumbItem } from '@/widgets/admin-layout';
import { STATUS_META, projectRoutes, type Project } from '@/entities/project';
import { type Task } from '@/entities/task';
import { ProjectKanban } from '@/widgets/project-kanban';

interface Props {
    project: {
        data: Project;
    };
    tasks: {
        data: Task[];
    };
}

const props = defineProps<Props>();

const project = computed(() => props.project.data);

const tasks = computed(() => props.tasks.data);

const status = computed(() => STATUS_META[project.value.status]);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { label: 'Проєкти', href: projectRoutes.index() },
    { label: project.value.title },
]);
</script>

<template>
    <Head :title="project.title" />

    <AdminLayout
        :title="project.title"
        :description="project.description"
        :breadcrumbs="breadcrumbs"
    >
        <template #actions>
            <Tag :value="status.label" :severity="status.severity" />
        </template>

        <div class="flex h-full flex-col gap-4 p-4">
            <ProjectKanban :tasks="tasks" />
        </div>
    </AdminLayout>
</template>
