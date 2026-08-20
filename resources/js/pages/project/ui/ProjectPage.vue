<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { AdminLayout, type BreadcrumbItem } from '@/widgets/admin-layout';
import { STATUS_META, projectRoutes, type Project } from '@/entities/project';

interface Props {
    project: {
        data: Project;
    };
}

const props = defineProps<Props>();

const project = computed(() => props.project.data);

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
            <div class="flex flex-1 flex-col items-center justify-center gap-3 rounded-lg border border-dashed border-gray-300 py-16 dark:border-gray-600">
                <i class="pi pi-table text-3xl text-gray-500"></i>
                <p class="text-gray-500">Тут буде Kanban-дошка</p>
            </div>
        </div>
    </AdminLayout>
</template>
