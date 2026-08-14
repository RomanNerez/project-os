<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { AdminLayout } from '@/widgets/admin-layout';
import { ProjectCard, type Project } from '@/entities/project';
import { ProjectFormModal } from '@/features/project-form';

interface Props {
  projects: {
    data: Project[];
    meta: {
        include: string[],
        pagination: {
            count: number;
            current_page: number;
            links: {};
            per_page: number;
            total: number;
            total_pages: number;
        }
    }
  }
}

const props = defineProps<Props>();

const isModalOpen = ref(false);
const editingProject = ref<Project | null>(null);

function openCreate(): void {
    editingProject.value = null;
    isModalOpen.value = true;
}

function openEdit(project: Project): void {
    editingProject.value = project;
    isModalOpen.value = true;
}

</script>

<template>
    <Head title="Проєкти" />

    <AdminLayout
        title="Проєкти"
        description="Керуйте проєктами робочого простору"
    >
        <template #actions>
            <Button label="Новий проєкт" icon="pi pi-plus" @click="openCreate" />
        </template>

        <div class="flex h-full flex-col gap-4 p-4">
            <div v-if="props.projects.data.length" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <ProjectCard
                    v-for="project in props.projects.data"
                    :key="project.id"
                    :project="project"
                    @edit="openEdit(project)"
                />
            </div>

            <div v-else class="flex flex-col items-center gap-3 rounded-lg border border-dashed border-surface-300 py-16 dark:border-surface-600">
                <i class="pi pi-folder-open text-3xl text-muted-color"></i>
                <p class="text-muted-color">Проєктів поки немає</p>
                <Button label="Створити перший проєкт" icon="pi pi-plus" text @click="openCreate" />
            </div>

            <ProjectFormModal
                v-model:visible="isModalOpen"
                :project="editingProject"
            />
        </div>
    </AdminLayout>
</template>
