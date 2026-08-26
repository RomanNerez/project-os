<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { AdminLayout } from '@/widgets/admin-layout';
import { ProjectCard, type Project } from '@/entities/project';
import { ProjectFormModal } from '@/features/project-form';
import { ProjectDeleteModal } from '@/features/project-delete';
import { EmptyList } from '@/shared/ui';

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

const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedProject = ref<Project | null>(null);

function openCreate(): void {
    selectedProject.value = null;
    isEditModalOpen.value = true;
}

function openEdit(project: Project): void {
    selectedProject.value = project;
    isEditModalOpen.value = true;
}

function openDelete(project: Project): void {
    selectedProject.value = project;
    isDeleteModalOpen.value = true;
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

        <div class="flex h-full flex-col gap-4">
            <div v-if="props.projects.data.length" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <ProjectCard
                    v-for="project in props.projects.data"
                    :key="project.id"
                    :project="project"
                    @edit="openEdit(project)"
                    @delete="openDelete(project)"
                />
            </div>

            <EmptyList
                v-else
                icon-class="pi-folder-open"
                decription="Проєктів поки немає"
                button-label="Створити перший проєкт"
                @on-handler="openCreate"
            />

            <ProjectFormModal
                v-model:visible="isEditModalOpen"
                :project="selectedProject"
                @done="selectedProject = null"
                @cancel="selectedProject = null"
            />

            <ProjectDeleteModal
                v-model:visible="isDeleteModalOpen"
                :project="selectedProject"
                @done="selectedProject = null"
                @cancel="selectedProject = null"
            />
        </div>
    </AdminLayout>
</template>
